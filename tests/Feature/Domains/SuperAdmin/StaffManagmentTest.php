<?php

declare(strict_types=1);

// cant create staff if not authenticated

// can create staff
it('can create staff', function (): void {
    $response = $this->get('/');

    $response->assertStatus(200);
});

// can update staff

// can soft delete staff
