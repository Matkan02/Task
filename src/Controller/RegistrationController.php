<?php
declare(strict_types=1);
namespace App\Controller;


use App\Service\RegistrationService;
use Symfony\Component\HttpClient\Exception\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Routing\Attribute\Route;

//admin: mateusz mateusz!
class RegistrationController extends AbstractController
{

    private $registrationService;

    public function __construct(RegistrationService $registrationService)
    {
        $this->registrationService = $registrationService;
    }
    #[Route('/register', name: 'registration', methods: 'POST')]
    public function registeruser(Request $request): JsonResponse
    {
        $userdata = json_decode($request->getContent(),true);


        try {
            $user = $this->registrationService->register($userdata);
            return $this->json($user, Response::HTTP_CREATED);
        }catch (InvalidArgumentException $e)
        {
            return $this->json(['error'=>$e->getMessage()],Response::HTTP_BAD_REQUEST);
        }
        catch (ConflictHttpException $e)
        {
            return $this->json(['error'=>$e->getMessage()],Response::HTTP_CONFLICT);
        }
        catch (Exception $e)
        {
            return $this->json(['error'=>$e->getMessage()],Response::HTTP_INTERNAL_SERVER_ERROR);
        }


    }
}
