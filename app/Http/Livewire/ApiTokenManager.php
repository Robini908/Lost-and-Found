<?php

namespace App\Http\Livewire;

use App\Models\User;
use App\Services\ApiTokenService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Jetstream\Jetstream;
use Livewire\Component;

class ApiTokenManager extends Component
{
    /**
     * The create API token form state.
     *
     * @var array
     */
    public $createApiTokenForm = [
        'name' => '',
        'type' => 'read',
        'permissions' => [],
        'expiration' => '7 days'
    ];

    /**
     * The update API token form state.
     *
     * @var array
     */
    public $updateApiTokenForm = [
        'permissions' => []
    ];

    /**
     * Indicates if the token creation modal is being displayed.
     *
     * @var bool
     */
    public $displayingToken = false;

    /**
     * Indicates if the permissions modal is being displayed.
     *
     * @var bool
     */
    public $managingApiTokenPermissions = false;

    /**
     * Indicates if token deletion is being confirmed.
     *
     * @var bool
     */
    public $confirmingApiTokenDeletion = false;

    /**
     * The ID of the token being managed.
     *
     * @var int|null
     */
    public $managingTokenId;

    /**
     * The newly created API token value.
     *
     * @var string|null
     */
    public $plainTextToken;

    /**
     * The token service instance.
     *
     * @var ApiTokenService
     */
    protected $tokenService;

    /**
     * Mount the component.
     *
     * @param ApiTokenService $tokenService
     * @return void
     */
    public function mount(ApiTokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    /**
     * Create a new API token.
     *
     * @return void
     */
    public function createApiToken()
    {
        $this->resetErrorBag();

        Validator::make([
            'name' => $this->createApiTokenForm['name'],
            'type' => $this->createApiTokenForm['type'],
            'expiration' => $this->createApiTokenForm['expiration'],
        ], [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:read,write,full'],
            'expiration' => ['required', 'string', 'in:24 hours,7 days,30 days,Never'],
        ])->validateWithBag('createApiToken');

        $user = Auth::user();
        if (!$user instanceof User) {
            throw new \RuntimeException('User not found');
        }

        $token = $this->tokenService->createToken(
            $user,
            $this->createApiTokenForm
        );

        $this->plainTextToken = $token->plainTextToken;
        $this->displayingToken = true;

        $this->createApiTokenForm = [
            'name' => '',
            'type' => 'read',
            'permissions' => [],
            'expiration' => '7 days'
        ];

        $this->emit('tokenCreated');
    }

    /**
     * Display the token's permissions modal.
     *
     * @param int $tokenId
     * @return void
     */
    public function manageApiTokenPermissions($tokenId)
    {
        $user = Auth::user();
        if (!$user instanceof User) {
            throw new \RuntimeException('User not found');
        }

        $this->managingTokenId = $tokenId;
        $this->managingApiTokenPermissions = true;

        $token = $user->tokens()->findOrFail($tokenId);
        $this->updateApiTokenForm['permissions'] = $token->abilities;
    }

    /**
     * Update the specified token's permissions.
     *
     * @return void
     */
    public function updateApiToken()
    {
        $this->resetErrorBag();

        $user = Auth::user();
        if (!$user instanceof User) {
            throw new \RuntimeException('User not found');
        }

        $this->tokenService->updateTokenPermissions(
            $user,
            $this->managingTokenId,
            $this->updateApiTokenForm['permissions']
        );

        $this->managingApiTokenPermissions = false;
        $this->emit('tokenUpdated');
    }

    /**
     * Confirm that the given API token should be deleted.
     *
     * @param int $tokenId
     * @return void
     */
    public function confirmApiTokenDeletion($tokenId)
    {
        $this->managingTokenId = $tokenId;
        $this->confirmingApiTokenDeletion = true;
    }

    /**
     * Delete the API token.
     *
     * @return void
     */
    public function deleteApiToken()
    {
        $user = Auth::user();
        if (!$user instanceof User) {
            throw new \RuntimeException('User not found');
        }

        $this->tokenService->deleteToken(
            $user,
            $this->managingTokenId
        );

        $this->confirmingApiTokenDeletion = false;
        $this->emit('tokenDeleted');
    }

    /**
     * Get the token statistics.
     *
     * @param int $tokenId
     * @return array
     */
    public function getTokenStats($tokenId)
    {
        $user = Auth::user();
        if (!$user instanceof User) {
            throw new \RuntimeException('User not found');
        }

        return $this->tokenService->getTokenStats($user, $tokenId);
    }

    /**
     * Get the current user.
     *
     * @return User
     * @throws \RuntimeException
     */
    public function getUserProperty(): User
    {
        $user = Auth::user();
        if (!$user instanceof User) {
            throw new \RuntimeException('User not found');
        }
        return $user;
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('api.api-token-manager');
    }
}
