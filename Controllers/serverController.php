<?php

namespace App\Controllers;
use GuzzleHttp\Client;

class ServerController extends BaseController
{
    function __construct() {
        parent::__construct("");
    }

    public function listServers()
    {
        try {
            
            $client = new Client();
            $result = [];
            $uri = "http://10.151.232.11:8774/v2.1/os-simple-tenant-usage/".$this->projectID;
            $response = $client->request('GET',$uri,[
                'headers' => [
                    'X-Auth-Token' => $this->authToken,
                ],
                'body' => "{
                }"]);
            $json_str = json_decode($response->getBody()->getContents())->tenant_usage->server_usages;
            $num = 0;
            foreach($json_str as $server)
            {
                $temp["key"] = $num++;
                $temp["name"] = $server->name;
                $temp["memory_mb"] = strval($server->memory_mb)." MB";
                $temp["vcpus"] = $server->vcpus;
                $temp["uptime"] = $server->uptime;
                $temp["status"] = $server->state;
                $temp["ip"] = [];
                $temp["id"] = $server->instance_id;
                $result[$server->instance_id] = $temp;
            }

            $uri ="http://10.151.232.11:8774/v2.1/servers/detail";
            $response = $client->request('GET',$uri,[
                'headers' => [
                    'X-Auth-Token' => $this->authToken,
                ],
                'body' => "{
                }"]);
            $json_str = json_decode($response->getBody()->getContents())->servers;
            foreach($json_str as $server)
            {
                if($server->status !="ERROR")
                {
                    foreach($server->addresses as $key=>$value)
                    {
                        $result[$server->id]["ip"] = $value[0]->addr; 
                        break;
                    }
                }
            }
            $result_n  =[];
            foreach($result as $item)
            {
                array_push($result_n,$item);
            }

            return respond($result_n,200);
        } catch (\Throwable $e) {
            return respond($e->getMessage(), 201);
        }
    }

    public function startServer()
    {
         try {
             $client = new Client();
             $uri = "http://10.151.232.11:8774/v2.1/servers/".request("id")."/action";
             $client->request('POST',$uri,[
                 'headers' => [
                     'X-Auth-Token' => $this->authToken,
                 ],
                 'body' => "{
                     \"os-start\": null
                 }"
             ]);
             return respond("Successfully started",200);
         } catch (\Throwable $e) {
             return respond($e->getMessage(), 201);
     }
    }
  
    public function stopServer()
   {
        try {
            $client = new Client();
            $uri = "http://10.151.232.11:8774/v2.1/servers/".request("id")."/action";
            $client->request('POST',$uri,[
                'headers' => [
                    'X-Auth-Token' => $this->authToken,
                ],
                'body' => "{
                    \"os-stop\": null
                }"
            ]);
            return respond("Successfully stopped",200);
        } catch (\Throwable $e) {
            return respond($e->getMessage(), 201);
    }
   }

   public function getServerDetails()
   {
        try {
            $client = new Client();
            $uri = "http://10.151.232.11:8774/v2.1/servers/".request("id");
            $response = $client->request('GET',$uri,[
                'headers' => [
                    'X-Auth-Token' => $this->authToken,
                ],
                'body' => "{
                }"
            ]);
            $json_str = json_decode($response->getBody()->getContents())->server;
            $result = [];
            
            if($json_str->status != "ERROR")
            {
                $result["key"] = $json_str->key_name;
                $result["created"] = $json_str->created;
                $result["updated"] = $json_str->updated;
                $result["network"] = [];
                $result["security"] = $json_str->security_groups[0]->name;
                foreach($json_str->addresses as $key => $value) {
                    $temp["name"] = $key;
                    $temp["ip"] = $value[0]->addr;
                    $temp["mac"] = $value[0]->{"OS-EXT-IPS-MAC:mac_addr"};
                    array_push($result["network"],$temp);
                }
            }
            else
            {
                $result["fault"] = $json_str->fault->message;
            }
            return respond($result,200);
        } catch (\Throwable $e) {
            return respond($e->getMessage(), 201);
    }

   }

   public function getFlavors()
   {    
        try {
            $client = new Client();
            $flavors_uri ="http://10.151.232.11:8774/v2.1/flavors/detail";
            $response = $client->request('GET',$flavors_uri,[
                'headers' => [
                    'X-Auth-Token' => $this->authToken,
                ],
                'body' => "{
                }"]);
            $json_str = json_decode($response->getBody()->getContents())->flavors;
            $result =$json_str;


            return respond($result,200);
        } catch (\Throwable $e) {
            return respond($e->getMessage(), 201);
        }
   }
   
   public function getServerActionLogs()
   {
        try {
            $client = new Client();
            $result = [];
            $uri = "http://10.151.232.11:8774/v2.1/servers/".request("id")."/os-instance-actions";
            $response = $client->request('GET',$uri,[
                'headers' => [
                    'X-Auth-Token' => $this->authToken,
                ],
                'body' => "{
                }"]);
            $json_str = json_decode($response->getBody()->getContents())->instanceActions;
            return respond($json_str,200);
        } catch (\Throwable $e) {
            return respond($e->getMessage(), 201);
        }
   }

   public function getServerImages()
   {
        try {
            $client = new Client();
            $result = [];
            $uri = "http://10.151.232.11:8774/v2.1/servers/".request("id")."/os-instance-actions";
            $response = $client->request('GET',$uri,[
                'headers' => [
                    'X-Auth-Token' => $this->authToken,
                ],
                'body' => "{
                }"]);
            $json_str = json_decode($response->getBody()->getContents())->instanceActions;
            return respond($json_str,200);
        } catch (\Throwable $e) {
            return respond($e->getMessage(), 201);
        }
   }


   public function getAllServerInfo()
   {    
        try {
            $client = new Client();
            $flavors_uri = "http://10.151.232.11:8774/v2.1/flavors";
            $response = $client->request('GET',$flavors_uri,[
                'headers' => [
                    'X-Auth-Token' => $this->authToken,
                ],
                'body' => "{
                }"]);
            $flavors = json_decode($response->getBody()->getContents())->flavors;
            $images_uri ="http://10.151.232.11:8774/v2.1/images";
            $response = $client->request('GET',$images_uri,[
                'headers' => [
                    'X-Auth-Token' => $this->authToken,
                ],
                'body' => "{
                }"]);
            $images = json_decode($response->getBody()->getContents())->images;

            $keys_uri = "http://10.151.232.11:8774/v2.1/os-keypairs";
            $response = $client->request('GET',$keys_uri,[
                'headers' => [
                    'X-Auth-Token' => $this->authToken,
                ],
                'body' => "{
                }"]);
            $keys = json_decode($response->getBody()->getContents())->keypairs;
            $networks_uri ="http://10.151.232.11:8774/v2.1/os-networks";
            $response = $client->request('GET',$networks_uri,[
                'headers' => [
                    'X-Auth-Token' => $this->authToken,
                ],
                'body' => "{
                }"]);

            $networks = json_decode($response->getBody()->getContents())->networks;
            $result["flavors"] = [];
            $result["images"] = [];
            $result["keys"] = [];
            $result["networks"] = [];

            foreach($flavors as $item)
            {   
                $temp["label"] = $item->name;
                $temp["value"] = $item->id;
                array_push($result["flavors"],$temp);
            }
            foreach($images as $item)
            {   
                $temp["label"] = $item->name;
                $temp["value"] = $item->id;
                array_push($result["images"],$temp);
            }
            foreach($keys as $item)
            {   
                $temp["label"] = $item->keypair->name;
                $temp["value"] = $item->keypair->fingerprint;
                array_push($result["keys"],$temp);
            }
            foreach($networks as $item)
            {   
                $temp["label"] = $item->label;
                $temp["id"] = $item->id;
                $temp["gateway"] = $item->gateway;
                $temp["cidr"] = $item->cidr;
                $temp["dns1"] = $item->dns1;
                $temp["dns2"] = $item->dns2;
                $temp["broadcast"] = $item->broadcast;

                array_push($result["networks"],$temp);
            }
            return respond($result,200);
        } catch (\Throwable $e) {
            return respond($e->getMessage(), 201);
        }
   }
   
   public function getServerGroup(){
        try{
            $client = new Client();
            $groups_uri = "http://10.151.232.11:8774/v2.1/os-server-groups";
            $response = $client->request('GET',$groups_uri,[
                'headers' => [
                    'X-Auth-Token' => $this->authToken,
                ],
                'body' => "{
                }"]);
            $result = json_decode($response->getBody()->getContents())->server_groups;
            return respond($result,200);
        }
            catch (\Throwable $e) {
                return respond($e->getMessage(), 201);
        }
   }

   public function getAllVolumes(){
        try{
            $client = new Client();
            $volumes_uri = "http://10.151.232.11:8776/v3/".$this->projectID."/volumes/detail";
            $response = $client->request('GET',$volumes_uri,[
                'headers' => [
                    'X-Auth-Token' => $this->authToken,
                ],
                'body' => "{
                }"]);
            $volumes = json_decode($response->getBody()->getContents())->volumes;
            $result["info"] = [];
            foreach($volumes as $item)
                    {   
                        $temp["size"] = $item->size;
                        $temp["status"] = $item->status;
                        if($item->attachments ) {
                            $temp["server_id"] = $item->attachments[0]->server_id;
                        }
                        if(isset($item->volume_image_metadata->image_name)){
                            $temp['image_name'] = $item->volume_image_metadata->image_name;
                        }

                        array_push($result["info"],$temp);

                    }
            return respond($result,200);
        }catch (\Throwable $e) {
            return respond($e->getMessage(), 201);
        }

   }


   public function addServer()
   {
    $data = json_decode(request("data"));
    $flavor = $data->flavor;
    $image = $data->image;
    $key = $data->keypair;
    $networks = $data->networks;
    $name = $data->name;
    $network_str = "[";
    foreach($networks as $network)
    {
        $network_str = $network_str."{\"uuid\":\"".$network."\"},";
    }
    $network_str = substr($network_str,0,strlen($network_str)-1) . "]";
    try {
        $client = new Client();
        $uri = "http://10.151.232.11:8774/v2.1/servers";
        $client->request('POST',$uri,[
            'headers' => [
                'X-Auth-Token' => $this->authToken
            ],
            'body' => "{
                \"server\": {
                    \"name\": \"".$name."\",
                    \"imageRef\" : \"".$image."\",
                    \"flavorRef\" : \"".$flavor."\",
                    \"networks\": ".$network_str.",
                    \"key_name\": \"".$key."\"
                }
            }"]);
        return respond("Server ".$name." created successfully",200);
    } catch (\Throwable $e) {
        return respond($e->getMessage(), 201);
    }
   }
   
}