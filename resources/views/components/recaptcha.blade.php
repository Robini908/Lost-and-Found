<!-- Enhanced reCAPTCHA Component -->
<div class="w-full">
    <!-- reCAPTCHA Container with custom styling -->
    <div class="relative">
        <div class="g-recaptcha transform hover:scale-[1.02] transition-transform duration-300"
             data-sitekey="{{ config('services.recaptcha.site_key') }}"
             data-theme="light"
             data-size="normal"
             data-callback="onRecaptchaSuccess"
             data-expired-callback="onRecaptchaExpired">
        </div>

        <!-- Loading State Overlay -->
        <div id="recaptcha-loading" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center hidden">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        </div>
    </div>

    <!-- Error Message -->
    @error('recaptcha')
        <div class="mt-2 flex items-center space-x-2 text-red-600 bg-red-50 px-3 py-2 rounded-lg">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm font-medium">{{ $message }}</span>
        </div>
    @enderror
</div>

@push('scripts')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
    function onRecaptchaSuccess() {
        document.getElementById('recaptcha-loading').classList.add('hidden');
        // Add a subtle success animation
        const recaptchaContainer = document.querySelector('.g-recaptcha');
        recaptchaContainer.style.transform = 'scale(1.02)';
        setTimeout(() => {
            recaptchaContainer.style.transform = 'scale(1)';
        }, 200);
    }

    function onRecaptchaExpired() {
        document.getElementById('recaptcha-loading').classList.add('hidden');
    }

    // Show loading state when reCAPTCHA is being solved
    document.addEventListener('DOMContentLoaded', () => {
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
                    const iframe = document.querySelector('.g-recaptcha iframe');
                    if (iframe && iframe.style.height !== '0px') {
                        document.getElementById('recaptcha-loading').classList.remove('hidden');
                    }
                }
            });
        });

        const recaptchaElement = document.querySelector('.g-recaptcha iframe');
        if (recaptchaElement) {
            observer.observe(recaptchaElement, { attributes: true });
        }
    });
</script>
@endpush
