<?php

namespace App\Services;

use App\Entity\Requisition;
use App\Repository\RequisitionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\CachingHttpClient;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpKernel\HttpCache\Store;
use Symfony\Component\Validator\Constraints\Ulid;

class NordigenClient{
  private $client;
  private $token;
  private $refresh_token;
  private $entityManager;
  const SANDBOX_INSTITUTION = 'SANDBOXFINANCE_SFIN0000';

  public function __construct(EntityManagerInterface $entityManager){
    $store = new Store('/var/cache/psd_rr');
    $client = new CurlHttpClient();
    $this->client = new CachingHttpClient($client, $store);
    $this->entityManager = $entityManager;
  }

  private function API_Post(string $endpoint, array $data){
    return $this->client->request('POST', $endpoint, [
      'headers' => [
        'Authorization' => sprintf('Bearer %s', $this->token),
        'Accept' => 'application/json',
        'Content-Type' => 'application/json'
      ],
      'json' => $data
    ])->toArray(false);
  }

  private function auth_GetInstitutions($countryCode = 'hu'){
    return $this->client->request('GET', 'https://ob.nordigen.com/api/v2/institutions/?country=' . $countryCode, [
      'headers' => [
        'Accept' => 'application/json',
        'Authorization' => sprintf('Bearer %s', $this->token)
      ]
    ])->getContent();
  }

  private function auth_createUserAgreement($institution = NordigenClient::SANDBOX_INSTITUTION){

    return $this->API_Post('https://ob.nordigen.com/api/v2/agreements/enduser/', [
      'institution_id' => $institution,
      'max_historical_days' => "90",
      'access_valid_for_days' => '30',
      'access_scope' => [
        'balances',
        'details',
        'transactions'
      ]
    ]);


  }

  private function auth_createRequisition(){
    $resp = $this->auth_createUserAgreement();
    $agreement = $resp['id'];
    $inst = $resp['institution_id'];
    $r = $this->API_Post('https://ob.nordigen.com/api/v2/requisitions/',
      [
        'redirect' => 'http://127.0.0.1:8001/psd2/auth',
        'institution_id' => $inst,
        'reference' => sha1(time()),
        'agreement' => $agreement,
        'user_language' => 'EN'
      ]);
    $k = new Requisition();
    $k->setToken($r['id']);
    $k->setUid(1);
    $this->entityManager->persist($k);
    return $r['link'];
  }

  public function getInstitutions($countryCode = 'hu'){
    return $this->auth_GetInstitutions($countryCode);
  }


  public function createRequisition(){
    return $this->auth_createRequisition();
  }

  public function getToken($secret_id = "01269712-3130-49e6-97ff-5e04e3a54e75", $secret_key = "ef681baf872ef7ebbf93150ed9c49608a13a0d92f1169d2fa37454bb38fffb86fa442870248b614f2ddeded042c6c41d06c2d1b84dc7572dc84f6124e702eb73"){
    $r = $this->API_Post('https://ob.nordigen.com/api/v2/token/new/', [
      'secret_id' => $secret_id,
      'secret_key' => $secret_key
    ]);
    $this->token = $r['access'];
    $this->refresh_token = $r['refresh'];
  }


}