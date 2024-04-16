<?php

namespace App\Action\test\Service;

use App\Data\ProfileCard\ProfileCard;
use App\Util\Slicer;

class ProfilesOnPageService
{
    private const PROFILES_ON_PAGE = 18;

    private Slicer $slicer;

    /**
     * @var ProfileCard[] - карточки
     */
    private array $profiles;

    public function __construct(
        Slicer $slicer,
        array $profiles,
    )
    {
        $this->slicer = $slicer;
        $this->profiles = $profiles;
    }

    /**
     * @param int $page
     * @return ProfileCard[]
     */
    public function getSlicedProfiles(int $page): array
    {
        return $this->slicer->slice(
            $this->profiles,
            self::PROFILES_ON_PAGE * ($page - 1),
            self::PROFILES_ON_PAGE
        );
    }

    public function getLastPage(): int
    {
        return ceil(count($this->profiles) / self::PROFILES_ON_PAGE);
    }
}