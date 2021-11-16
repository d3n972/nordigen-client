<?php

namespace App\Controller;

use App\Services\NordigenClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\CachingHttpClient;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpCache\Store;
use Symfony\Component\Routing\Annotation\Route;

class PSD2Controller extends AbstractController{
  protected NordigenClient $nordigen;

  public function __construct(NordigenClient $nordigen){
    $this->nordigen = $nordigen;

  }

  #[Route('/psd2', name: 'psd2')]
  public function index(): Response{
    $this->nordigen->getToken();
    $this->nordigen->getInstitutions();

    dd($this->nordigen->createRequisition());

    return $this->render('psd2/index.html.twig', [
      'controller_name' => 'PSD2Controller',
    ]);
  }

  #[Route('/psd2/auth', name: 'psd2.auth')]
  public function auth(Request $request){
    dd($request->query->get('ref'));
  }
}
