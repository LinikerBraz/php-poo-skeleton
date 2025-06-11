<?php

namespace Hogwarts\Models\Module6;

use Hogwarts\Models\Base\Entity;

class Communication extends Entity
{
    private int $senderId;
    private string $senderType; // 'student', 'professor', 'staff'
    private array $recipients = [];
    private string $subject;
    private string $content;
    private string $type; // 'email', 'notification', 'announcement'
    private bool $urgent = false;
    private array $attachments = [];

    public function __construct(int $senderId, string $senderType, string $subject, string $content, string $type = 'notification')
    {
        parent::__construct();
        $this->senderId = $senderId;
        $this->senderType = $senderType;
        $this->subject = $subject;
        $this->content = $content;
        $this->type = $type;
    }

    public function getSenderId(): int
    {
        return $this->senderId;
    }

    public function getSenderType(): string
    {
        return $this->senderType;
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
            'delivered' => false,
            'deliveredAt' => null,
            'read' => false,
            'readAt' => null
        ];
        $this->updateTimestamp();
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function isUrgent(): bool
    {
        return $this->urgent;
    }

    public function setUrgent(bool $urgent): void
    {
        $this->urgent = $urgent;
        $this->updateTimestamp();
    }

    public function getAttachments(): array
    {
        return $this->attachments;
    }

    public function addAttachment(string $filename, string $path): void
    {
        $this->attachments[] = [
            'filename' => $filename,
            'path' => $path,
            'uploadedAt' => new \DateTime()
        ];
        $this->updateTimestamp();
    }

    public function markAsDelivered(int $userId): void
    {
        foreach ($this->recipients as &$recipient) {
            if ($recipient['userId'] === $userId) {
                $recipient['delivered'] = true;
                $recipient['deliveredAt'] = new \DateTime();
                break;
            }
        }
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
}
