<?php

namespace App\Controllers;
use App\Helpers\DB;

use GuzzleHttp\Client;

class BaseController
{
	protected $authToken;
	protected $projectID;

    function __construct()
    {
		$projectName = "Liman";
		$user = "aakirman";
		$pass = "Passw0rd!!!";
		$client = new Client();
        $uri = "http://10.151.232.11:5000/v3/auth/tokens";
		if($projectName == "")
		{
			$response = $client->request('POST',$uri,[
				'headers' => [
					'Content-Type' => 'application/json',
				],
				'body' => "{
					\"auth\": {
					  \"identity\": {
						\"methods\": [
							\"password\"],
						\"password\": {
							\"user\": {
								\"name\": \"".$user."\",
								\"domain\": {
									\"id\": \"default\"
							},
							\"password\": \"".$pass."\"
						  }
						}
					  }
					}
				  }"]);
			$this->authToken = $response->getHeader("X-Subject-Token")[0];
            
		}
		else
		{
			$response = $client->request('POST',$uri,[
				'headers' => [
					'Content-Type' => 'application/json',
				],
				'body' => "{
					\"auth\": {
					  \"identity\": {
						\"methods\": [
							\"password\"
						],
						\"password\": {
							\"user\": {
								\"name\": \"".$user."\",
								\"domain\": {
									\"id\": \"default\"
							},
							\"password\": \"".$pass."\"
						  }
						}
					  },
					  \"scope\": {
						\"project\": {
							\"name\": \"".$projectName."\",
							\"domain\": {
								\"id\": \"default\"
						  }
						}
					  }
					}
				  }"]);
			$this->authToken = $response->getHeader("X-Subject-Token")[0];
			$this->projectID = json_decode($response->getBody()->getContents())->token->project->id;
		}
        
    }
}