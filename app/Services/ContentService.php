<?php namespace App\Services;

use App\Cloud;

class ContentService {

    private function isFile($content, $cloudType)
    {
        if($cloudType === Cloud::YandexDisk) {
            if(count($content) === 1) {
                return true;
            }
        }
        elseif($cloudType === Cloud::DropBox) {
            if(count($content) === 2 && is_string($content[0])) {
                return true;
            }
        }
    }

    public function getContents($contents, $cloud)
    {
        $response = [];

        //If it for download that no need handle data
        if($this->isFile($contents, $cloud->type)) {
            return $contents;
        }

        if($cloud->type === Cloud::DropBox) {
            foreach($contents as $content) {
                //Create common interface
                $temp = [
                    'is_dir' => $content['is_dir'],
                    'path' => $content['name'],
                    'size' => $content['size'],
                    'updated_at' => $content['modified'],

                    'cloud_type' => $cloud->type,
                    'cloud_name' => $cloud->name
                ];

                //Add to response
                $response[] = $temp;
            }
        }
        elseif($cloud->type === Cloud::YandexDisk) {

            if(!is_array($contents)) {
                return [$contents];
            }

            array_shift($contents);
            foreach($contents as $content) {
                //Create common interface
                $temp = [
                    'is_dir' => ($content['resourceType'] === 'dir'),
                    'path' => $content['href'],
                    'size' => $content['contentLength'],
                    'updated_at' => $content['lastModified'],
                    'display_name' => $content['displayName'],

                    'cloud_type' => $cloud->type,
                    'cloud_name' => $cloud->name
                ];

                //Add to response
                $response[] = $temp;
            }
        }

        return $response;
    }
}