<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ItemClaim;
use Illuminate\Support\Facades\Auth;
use App\Services\HashIdService;

class ItemClaimVerification extends Component
{
    public $claim;
    public $verificationNotes;
    public $verificationMethod;
    public $requiresInPerson = false;
    public $expirationDays = 7;
    public $showVerificationModal = false;
    public $showRejectionModal = false;
    public $rejectionNotes;
    public $verificationData = [];
    public $verificationSteps = [];
    public $currentStep = 1;

    protected $rules = [
        'verificationNotes' => 'nullable|string|max:1000',
        'verificationMethod' => 'required|string',
        'requiresInPerson' => 'boolean',
        'expirationDays' => 'required|integer|min:1|max:30',
        'rejectionNotes' => 'required_if:showRejectionModal,true|string|max:1000',
    ];

    protected HashIdService $hashIdService;

    public function boot(HashIdService $hashIdService)
    {
        $this->hashIdService = $hashIdService;
    }

    public function mount($claimId = null)
    {
        if ($claimId) {
            $decodedId = $this->hashIdService->decode($claimId);
            abort_if(!$decodedId, 404);

            $this->claim = ItemClaim::with(['user', 'lostItem', 'verifier'])
                ->findOrFail($decodedId);

            // Rate limiting for verification attempts
            $key = 'claim_verification_' . $this->claim->id;
            abort_if(
                cache()->get($key, 0) > 5,
                429,
                'Too many verification attempts'
            );
            cache()->increment($key);
            cache()->expire($key, now()->addMinutes(15));

            $this->initializeVerificationSteps();
        }
    }

    protected function initializeVerificationSteps()
    {
        $this->verificationSteps = [
            [
                'title' => 'Basic Information',
                'description' => 'Review basic claim information',
                'completed' => true
            ],
            [
                'title' => 'Identity Verification',
                'description' => 'Verify claimant identity',
                'completed' => false
            ],
            [
                'title' => 'Item Details',
                'description' => 'Verify item ownership/details',
                'completed' => false
            ],
            [
                'title' => 'Final Decision',
                'description' => 'Make final verification decision',
                'completed' => false
            ]
        ];
    }

    public function nextStep()
    {
        if ($this->currentStep < count($this->verificationSteps)) {
            $this->verificationSteps[$this->currentStep - 1]['completed'] = true;
            $this->currentStep++;
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function verifyClaim()
    {
        $this->validate([
            'verificationNotes' => 'required|string|max:1000',
            'verificationMethod' => 'required|string',
        ]);

        try {
            $this->claim->verify(
                Auth::user(),
                $this->verificationNotes,
                [
                    'method' => $this->verificationMethod,
                    'verification_data' => $this->verificationData
                ]
            );

            if ($this->requiresInPerson) {
                $this->claim->requireInPersonVerification(
                    now()->addDays($this->expirationDays)
                );
            }

            $this->showVerificationModal = false;
            $this->emit('claimVerified', $this->claim->id);

            // Notify the user
            $this->dispatchBrowserEvent('toast', [
                'title' => 'Success!',
                'message' => 'Claim has been verified successfully.',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('toast', [
                'title' => 'Error!',
                'message' => 'Failed to verify claim. Please try again.',
                'type' => 'error'
            ]);
        }
    }

    public function rejectClaim()
    {
        $this->validate([
            'rejectionNotes' => 'required|string|max:1000'
        ]);

        try {
            $this->claim->reject(
                Auth::user(),
                $this->rejectionNotes
            );

            $this->showRejectionModal = false;
            $this->emit('claimRejected', $this->claim->id);

            $this->dispatchBrowserEvent('toast', [
                'title' => 'Success!',
                'message' => 'Claim has been rejected.',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('toast', [
                'title' => 'Error!',
                'message' => 'Failed to reject claim. Please try again.',
                'type' => 'error'
            ]);
        }
    }

    public function updateVerificationData($key, $value)
    {
        $this->verificationData[$key] = $value;
    }

    public function getHashedId()
    {
        return $this->claim ? $this->hashIdService->encode($this->claim->id) : null;
    }

    public function render()
    {
        return view('livewire.item-claim-verification');
    }
}
