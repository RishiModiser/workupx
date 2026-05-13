<?php

declare(strict_types=1);

function email_template_welcome(string $name): string
{
    return "Hello {$name},\n\nWelcome to WORKUPX. Your account has been created successfully.\n\nStay informed with educational trade signals and community updates.\n\nRegards,\nWORKUPX Team";
}

function email_template_reset(string $name, string $link): string
{
    return "Hello {$name},\n\nWe received a password reset request for your WORKUPX account.\nReset link: {$link}\n\nIf you did not request this, please ignore this email.\n\nRegards,\nWORKUPX Team";
}
