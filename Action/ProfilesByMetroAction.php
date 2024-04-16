<?php

namespace App\Action\test\Action;

use App\Builder\Profile\ProfileCardsBuilder;
use App\Model\MetroModel;
use App\Repository\MetroRepository;
use App\Repository\ProfileRepository;
use App\Service\ProfilesOnPageService;
use App\Util\Slicer;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ProfilesByMetroAction
{
    private Twig $twig;

    private ProfileRepository $profileRepository;

    private ProfileCardsBuilder $profileCardsBuilder;

    private MetroRepository $metroRepository;

    private Slicer $slicer;

    public function __construct(
        Twig $twig,
        ProfileRepository $profileRepository,
        ProfileCardsBuilder $profileCardsBuilder,
        Slicer $slicer,
        MetroRepository $metroRepository,
    ) {
        $this->twig = $twig;
        $this->profileRepository = $profileRepository;
        $this->profileCardsBuilder = $profileCardsBuilder;
        $this->slicer = $slicer;
        $this->metroRepository = $metroRepository;
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $page = $request->getQueryParams()['page'] ?? 1;
        $metroDescriptor = $args['descriptor'];

        /** @var MetroModel $metroModel */
        $metroModel = $this->metroRepository->getMetroByDescriptor($metroDescriptor);
        $catalogHeaderTitle = 'Метро: ' . $metroModel->getName();

        $profiles = $this->profileRepository->getProfilesByMetro($metroModel->getId())->toArray();
        $profiles = $this->profileCardsBuilder->build($profiles);

        $profilesOnPageService = new ProfilesOnPageService($this->slicer, $profiles);
        $profilesOnPage = $profilesOnPageService->getSlicedProfiles($page);
        $lastPage = $profilesOnPageService->getLastPage();

        if ($page < 1 || $page > $lastPage) {
            throw new InvalidArgumentException();
        }

        return $this->twig->render(
            $response,
            'pages/profiles.twig',
            [
                'catalogHeaderTitle' => $catalogHeaderTitle,
                'profiles' => $profilesOnPage,
                'navigationResponse' => [
                    'lastPage' => $lastPage,
                    'currentPage' => $page,
                ],
            ],
        );
    }
}
