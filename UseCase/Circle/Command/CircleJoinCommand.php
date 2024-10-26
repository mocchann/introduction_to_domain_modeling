<?php

namespace UseCase\Circle\Command;

class CircleJoinCommand
{
    public function __construct(
        private string $circle_id,
        private string $user_id
    ) {
        $this->circle_id = $circle_id;
        $this->user_id = $user_id;
    }

    public function getCircleId(): string
    {
        return $this->circle_id;
    }
}
