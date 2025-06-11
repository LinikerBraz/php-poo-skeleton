<?php

namespace Hogwarts\Models\Module6;

use Hogwarts\Models\Base\Entity;

class Alert extends Entity
{
    private string $title;
    private string $message;
    private string $type; // 'info', 'warning', 'danger', 'success'
    private array $recipients = [];
    private bool $sent = false;
    private ?\DateTime $sentAt = null;
    private ?\DateTime $scheduledFor = null;

    public function __construct(string $title, string $message, string $type = 'info')
    {
        parent::__construct();
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getRecipients(): array
    {
        return $this->recipients;
    }

    public function addRecipient(int $userId, string $userType): void
    {
        $this->recipients[] = [
            'userId' => $userId,
            'userType' => $userType,
            'read' => false,
            'readAt' => null
        ];
        $this->updateTimestamp();
    }

    public function markAsRead(int $userId): void
    {
        foreach ($this->recipients as &$recipient) {
            if ($recipient['userId'] === $userId) {
                $recipient['read'] = true;
                $recipient['readAt'] = new \DateTime();
                break;
            }
        }
        $this->updateTimestamp();
    }

    public function isSent(): bool
    {
        return $this->sent;
    }

    public function send(): void
    {
        $this->sent = true;
        $this->sentAt = new \DateTime();
        $this->updateTimestamp();
    }

    public function getSentAt(): ?\DateTime
    {
        return $this->sentAt;
    }

    public function getScheduledFor(): ?\DateTime
    {
        return $this->scheduledFor;
    }

    public function scheduleFor(\DateTime $dateTime): void
    {
        $this->scheduledFor = $dateTime;
        $this->updateTimestamp();
    }

    public function isScheduled(): bool
    {
        return $this->scheduledFor !== null && !$this->sent;
    }

    public function shouldBeSent(): bool
    {
        return $this->isScheduled() && $this->scheduledFor <= new \DateTime();
    }
}
