<?php

namespace App\Http;

use App\Http\Helpers\Base,
    File,
    Storage,
    GuzzleHttp\Client,
    Illuminate\Http\Response,
    Dkd\PhpCmis\Enum\AclPropagation,
    Dkd\PhpCmis\DataObjects\ObjectId,
    Illuminate\Support\Facades\Config,
    Dkd\PhpCmis\DataObjects\Principal,
    Illuminate\Support\Facades\Session,
    GuzzleHttp\Exception\RequestException,
    Dkd\PhpCmis\DataObjects\AccessControlEntry,
    Dkd\PhpCmis\Exception\CmisObjectNotFoundException,
    Symfony\Component\HttpFoundation\File\UploadedFile,
    Dkd\PhpCmis\Exception\CmisContentAlreadyExistsException;


class Alfresco {

    public static function login($username, $password)
    {
        $client = new Client();

        try {
            $loginApi = Config::get('alfresco.ALFRESCO_API') . 'login';
            $request = $client->post($loginApi, [
                'json' => [
                    'username' => $username,
                    'password' => $password
                ]
            ]);

            $data = json_decode($request->getBody(), true);

            $response = [
                'status' => $request->getStatusCode(),
                'message' => $data['data']['ticket']
            ];
        } catch (RequestException $e) {
            $response = [
                'status' => 403,
                'message' => 'Credential not found'
            ];
        }

        return $response;
    }

    public static function validateUser($access_id)
    {
        $client = new Client();

        try {
            $apiTarget = Config::get('alfresco.ALFRESCO_API')
                . 'login/ticket/'
                . $access_id
                . '?alf_ticket=' . $access_id;
            $request = $client->get($apiTarget);

            return ($request->getStatusCode() == Config::get('constant.LOGIN_SUCCESS_STATUS'));
        } catch (RequestException $e) {
            return false;
        }
    }

    public static function logout($access_id)
    {
        $client = new Client();

        try {

            $client->delete(Config::get('alfresco.ALFRESCO_API') . 'login/ticket/' . $access_id . '?alf_ticket=' . $access_id);

            return TRUE;

        } catch (RequestException $e) {

            return false;
        }
    }

    public static function uploadDocument(
        $doc,               // document id from alfresco
        $user,              // username of document author
        $password,          // password of document author
        $params = array()   // array that contains ACE
        )
    {
        // getting alfresco session
        $alfresco = new Alfresco();
        $session = $alfresco->getAlfrescoSession($user, $password);


        // handling Directory

        $directory = null; // default directory container
        $dirBase = '/User Homes/';
        $path = $dirBase . $user;

        try {
            // try best case path
            $directory = $session
                ->getObjectByPath($dirBase . $user);
            $signedDir = null;
// echo $directory->getId();exit;
            // try {
            //     $properties = array(
            //         \Dkd\PhpCmis\PropertyIds::OBJECT_TYPE_ID => 'cmis:folder',
            //         \Dkd\PhpCmis\PropertyIds::NAME => 'upload'
            //     );
            //
            //     $signedDir = $session->createFolder(
            //         $properties,
            //         $session->createObjectId($directory->getId())
            //     );
            // } catch (CmisContentAlreadyExistsException $e) {
            //
            // }
        } catch (CmisObjectNotFoundException $e) {
            // get root directory
            $rootDirectory = $session
                ->getObjectByPath($dirBase);

            // create new folder with corresponding username
            $properties = array(
                \Dkd\PhpCmis\PropertyIds::OBJECT_TYPE_ID => 'cmis:folder',
                \Dkd\PhpCmis\PropertyIds::NAME => $user
            );

            // action of creating new folder
            $session->createFolder(
                $properties,
                $session->createObjectId($rootDirectory->getId())
            );

            // get newly created directory
            $directory = $session
                ->getObjectByPath($path);

            // $signedDir = null;
            //
            // try {
            //     $properties = array(
            //         \Dkd\PhpCmis\PropertyIds::OBJECT_TYPE_ID => 'cmis:folder',
            //         \Dkd\PhpCmis\PropertyIds::NAME => 'upload'
            //     );
            //
            //     $signedDir = $session->createFolder(
            //         $properties,
            //         $directory->getId()
            //     );
            // } catch (CmisContentAlreadyExistsException $e) {}
        }
        // ==================================================
        // =============== Uploading document ===============
        // ==================================================
        $uniqueFileName = $alfresco
        ->getUniqueFileName([
            'path' => $path,
            'filename' => $doc->getClientOriginalName(),
            'session' => $session
        ]);

        $properties = array(
            \Dkd\PhpCmis\PropertyIds::OBJECT_TYPE_ID => 'cmis:document',
            \Dkd\PhpCmis\PropertyIds::NAME => $uniqueFileName
        );

        // Creating ACEs
        $aces = [];
        // foreach ($params['assignees'] as $assignee) {
        //     if ($assignee !== null) {
        //         $aces[] = $session
        //         ->getObjectFactory()
        //         ->createAce(
        //             $assignee['username'],  // Username to be granted
        //             ['cmis:all']            // Grant all permissions
        //         );
        //     }
        // }

        try {
            $document = $session
            ->createDocument(
                $properties,
                $session->createObjectId(   // Creating object id interface
                    $directory->getId()     // from alfresco string obj id
                ),
                \GuzzleHttp\Stream\Stream::factory(
                  fopen($doc->getPath() . '/' . $doc->getFileName(), 'r')
                ),
                null,
                [],
                $aces
            );
        } catch (CmisContentAlreadyExistsException $e) {
            echo "********* ERROR **********<br/>";
            echo $e->getMessage() . "<br/>";
            echo "**************************<br/>";
            exit();
        }

        return $document->getId();
    }

    public function getAlfrescoSession($user = null, $password = null)
    {
        // ==================================================
        // ======== General Alfresco configuration ==========
        // ==================================================

        // Check if Alfresco configuration exists
        // $user = Config::get('alfresco.CMIS_BROWSER_USER');
        // $password = Config::get('alfresco.CMIS_BROWSER_PASSWORD');
        $isConfigExists =
        Config::get('alfresco.CMIS_BROWSER_URL') !== null
        && $user !== null
        && $password !== null;

        // Default configuration variables
        $url = $repository = null;

        if ( !$isConfigExists ) { // If it is not exist
            die("Add connection parameters in configuration file!");
        } else { // Otherwise, set the variables
            $url = Config::get('alfresco.CMIS_BROWSER_URL');
            $repository = Config::get('alfresco.CMIS_REPOSITORY_ID');
        }

        // HTTP invoker parameter
        $httpInvoker = new \GuzzleHttp\Client([
            'defaults' => [
                'auth' => [$user, $password],
            ]
        ]);

        // ==================================================
        // ========== Creating Alfresco session =============
        // ==================================================

        $sessionFactory = new \Dkd\PhpCmis\SessionFactory();
        $repositoryId = \Dkd\PhpCmis\SessionParameter::REPOSITORY_ID;
        $bindingType = \Dkd\PhpCmis\SessionParameter::BINDING_TYPE;
        $browserUrl = \Dkd\PhpCmis\SessionParameter::BROWSER_URL;
        $browserSuccinct = \Dkd\PhpCmis\SessionParameter::BROWSER_SUCCINCT;
        $httpInvokerObj = \Dkd\PhpCmis\SessionParameter::HTTP_INVOKER_OBJECT;

        $parameters = array(
            $bindingType => \Dkd\PhpCmis\Enum\BindingType::BROWSER,
            $browserUrl => $url,
            $browserSuccinct => false,
            $httpInvokerObj => $httpInvoker,
        );

        if ($repository === null) {
            $repositories = $sessionFactory
            ->getRepositories($parameters);
            $parameters[$repositoryId] = $repositories[0]
            ->getId();
        } else {
            $parameters[$repositoryId] = $repository;
        }

        $session = $sessionFactory->createSession($parameters);

        return $session;
    }

    private function getUniqueFileName($params)
    {
        try {
            $first = $params['session']
            ->getObjectByPath(
                $params['path']
                . '/'
                . $params['filename']
            );

            $i = 1;
            $uniqueFileName = $params['filename'];

            while (true) {
                $path_parts = pathinfo($params['filename']);
                $uniqueFileName = $path_parts['filename']
                . ' (' . $i . ').'
                . $path_parts['extension'];

                try {
                    $obj = $params['session']
                    ->getObjectByPath(
                        $params['path']
                        . '/'
                        . $uniqueFileName
                    );
                    $i++;
                } catch (CmisObjectNotFoundException $e) {
                    break;
                }
            }

            return $uniqueFileName;

        } catch (CmisObjectNotFoundException $e) {
            return $params['filename'];
        }
    }

}
