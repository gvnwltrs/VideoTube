<?php
class VideoProcessor {
    private $connection; 
    private $sizeLimit = 50000000;
    private $allowedTypes = array("mp4", "flv", "webm", "mkv", "vob", "ogv", "ogg", "avi", "wmv", "mov", "mpeg", "mpg"); 
    private $ffmpegPath = "ffmpeg/ffmpeg"; 
    private $ffprobePath = "ffmpeg/ffprobe"; 

    public function __construct($connection) {
        $this->connection = $connection; 
    }

    public function upload($videoUploadData) {
        $targetDir = "uploads/videos/"; 
        $videoData = $videoUploadData->videoDataArray; 

        $tempFilePath = $targetDir . uniqid() . basename($videoData["name"]);
        
        $tempFilePath = str_replace(" ", "_", $tempFilePath); 
        //after upload -> "uploads/videos/5aa3e934c9ff/dogs_playing.flv"

        $isValidData = $this->processData($videoData, $tempFilePath); 

        if(!$isValidData) {
            echo "Video data not valid.";
            return false;
        }

        // echo "Attempting to move video..." . "</br>"; 
        
        if(move_uploaded_file($videoData["tmp_name"], $tempFilePath)) {
            // echo "File moved successfully"; 

            $finalFilePath = $targetDir . uniqid() . ".mp4"; 

            if(!$this->insertVideoData($videoUploadData, $finalFilePath)) {
                echo "Insert query failed"; 
                return false;
            }

            if(!$this->convertVideoToMp4($tempFilePath, $finalFilePath)) {
                echo "Upload failed"; 
                return false; 
            }

            // clean up temporary files
            if(!$this->deleteFile($tempFilePath)) {
                echo "Upload failed"; 
                return false;
            } 

            if(!$this->generateThumbnails($finalFilePath)) {
                echo "Upload failed -- could not generate from thumbnails"; 
                return false;
            } 

            return true; 
        }
        else {
            echo "Something broke... ):";
        }

    }

    private function processData($videoData, $filePath) {
        $videoType = pathInfo($filePath, PATHINFO_EXTENSION); 

        if(!$this->isValidSize($videoData)) {
            echo "File too large. Can't be more than " . $this->sizeLimit . " bytes"; 
            return false; 
        }
        else if(!$this->isValidType($videoType)) {
            echo "Invalid file type"; 
            return false; 
        }
        else if($this->hasError($videoData)) {
            echo "Error code: " . $videoData["error"]; 
            return false; 
        }

        return true;  
    }

    private function isValidSize($data) {
        return $data["size"] <= $this->sizeLimit; 
    }

    private function isValidType($type) {
        $lowercased = strtolower($type); 
        return in_array($lowercased, $this->allowedTypes);
    }

    private function hasError($data) {
        return $data["error"] != 0; 
    }

    private function insertVideoData($uploadData, $filePath) {
        $query = $this->connection->prepare("INSERT INTO videos(title, uploadedBy, description, privacy, category, filePath)
                                                VALUES(:title, :uploadedBy, :description, :privacy, :category, :filePath)");

        $query->bindParam(":title", $uploadData->title); 
        $query->bindParam(":uploadedBy", $uploadData->uploadedBy); 
        $query->bindParam(":description", $uploadData->description); 
        $query->bindParam(":privacy", $uploadData->privacy); 
        $query->bindParam(":category", $uploadData->category); 
        $query->bindParam(":filePath", $filePath); 

        return $query->execute();
        
        // DEBUG
        // $query->execute();
        // var_dump($query->errorInfo()); 
        // return false; 
    }

    private function convertVideoToMp4($tempFilePath, $finalFilePath) {
        $cmd = "$this->ffmpegPath -i $tempFilePath $finalFilePath 2>&1"; 

        $outputLog = array(); 
        exec($cmd, $outputLog, $returnCode);

        if($returnCode != 0) {
            //command failed
            foreach($outputLog as $line) {
                echo $line . "<br>"; 
            }
            return false; 
        }

        return true; 
    }

    private function deleteFile($filePath) {
        if(!unlink($filePath)) {
            echo "Could not delete file\n"; 
            return false;
        }
        
        return true; 
    }

    private function generateThumbnails($filePath) {
        $thumbnailSize = "210x118"; 
        $numThumbnails = 3; 
        $pathToThumbnail = "uploads/videos/thumbnails"; 

        $duration = $this->getVideoDuration($filePath); 
        $videoId = $this->connection->lastInsertId();
        
        $this->updateDuration($duration, $videoId); 

        // echo "\n\nduration: $duration"; 

        for($num = 1; $num <= $numThumbnails; $num++) {
            $imageName = uniqid() . ".jpg";
            $interval = ($duration * 0.8) / $numThumbnails * $num; 
            $fullThumbnailPath = "$pathToThumbnail/$videoId-$imageName";
            $selected = $num == 1 ? 1 : 0; 

            $cmd = "$this->ffmpegPath -i $filePath -ss $interval -s $thumbnailSize -vframes 1 $fullThumbnailPath 2>&1"; 

            $outputLog = array(); 
            exec($cmd, $outputLog, $returnCode);

            if($returnCode != 0) {
                //command failed
                foreach($outputLog as $line) {
                    echo $line . "<br>"; 
                }
            } 

            $query = $this->connection->prepare("INSERT INTO thumbnails(videoId, filePath, selected)
                                                VALUES(:videoId, :filePath, :selected)");

            $query->bindParam(":videoId", $videoId);
            $query->bindParam(":filePath", $fullThumbnailPath);
            $query->bindParam(":selected", $selected);


            $success = $query->execute(); 

            if(!$success) {
                echo "Error inserting thumbnail\n"; 
                return false; 
            }
        }

        return true; 
    }

    private function getVideoDuration($filePath) {
        // echo "\n\ntrying to get video duration...";
        return (int)shell_exec("$this->ffprobePath -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 $filePath");
    }

    private function updateDuration($duration, $videoId) {
        $hours = floor($duration / 3600); 
        $minutes = floor(($duration - ($hours * 3600)) / 60);
        $seconds = floor($duration % 60); 
        
        $hours = ($hours < 1) ? "" : $hours . ":"; 
        $minutes = ($minutes < 10) ? "0" . $minutes . ":" : $minutes . ":"; 
        $seconds = ($seconds < 10) ? "0" . $seconds : $seconds;  

        $duration = $hours.$minutes.$seconds; 

        $query = $this->connection->prepare("UPDATE videos SET duration=:duration WHERE id=:videoId");
        $query->bindParam(":duration", $duration);
        $query->bindParam(":videoId", $videoId);
        $query->execute(); 
    }
}
?>