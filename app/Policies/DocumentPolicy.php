<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DocumentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view documents list
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Document $document): bool
    {
        // Users can view if they are the current holder, encoder, or belong to the current office
        return $user->id === $document->current_holder ||
               $user->id === $document->encoded_by ||
               $user->office_id === $document->current_office;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can create documents
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Document $document): bool
    {
        // Only current holder or encoder can update
        return $user->id === $document->current_holder ||
               $user->id === $document->encoded_by;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Document $document): bool
    {
        // Only encoder can delete
        return $user->id === $document->encoded_by;
    }

    /**
     * Determine whether the user can route the document.
     */
    public function route(User $user, Document $document): bool
    {
        // Only current holder can route documents
        return $user->id === $document->current_holder;
    }

    /**
     * Determine whether the user can receive the document.
     */
    public function receive(User $user, Document $document): bool
    {
        // Users can receive if they belong to the current office
        return $user->office_id === $document->current_office;
    }
}
