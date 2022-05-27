<?php

namespace App\Policies;

use App\Supplier_has_item;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SupplierHasItemPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Supplier_has_item  $supplierHasItem
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Supplier_has_item $supplierHasItem)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\Supplier_has_item  $supplierHasItem
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Supplier_has_item $supplierHasItem)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Supplier_has_item  $supplierHasItem
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Supplier_has_item $supplierHasItem)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\Supplier_has_item  $supplierHasItem
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Supplier_has_item $supplierHasItem)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Supplier_has_item  $supplierHasItem
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Supplier_has_item $supplierHasItem)
    {
        //
    }
}
