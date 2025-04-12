<div class="bg-white rounded-xl shadow-sm" x-data="{
    activeTab: 'personal',
    calculateProgress() {
        let filled = 0;
        let total = 19; // Total number of fields

        // Count filled fields from state
        Object.values($wire.state).forEach(value => {
            if (value && value.toString().trim() !== '') filled++;
        });

        // Count filled social links
        Object.values($wire.social).forEach(value => {
            if (value && value.toString().trim() !== '') filled++;
        });

        return Math.round((filled / total) * 100);
    }
}">
    <!-- Profile Completion Header -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-t-xl p-4 border-b">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-lg font-semibold text-gray-800">Profile Completion</h3>
            <span x-text="calculateProgress() + '%'" class="text-sm font-medium text-blue-600"></span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2.5">
            <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-500" x-bind:style="'width: ' + calculateProgress() + '%'"></div>
        </div>
        <div class="mt-2 flex items-center text-sm text-gray-600">
            <i class="fas fa-info-circle mr-2"></i>
            <span>Complete your profile to increase your visibility and trust in the community</span>
        </div>
    </div>

    <div class="p-4 sm:p-6">
        <!-- Tabs -->
        <nav class="flex space-x-1 rounded-lg bg-gray-50 p-1" aria-label="Tabs">
            <button @click="activeTab = 'personal'"
                    :class="{'bg-white text-blue-600 shadow-sm': activeTab === 'personal',
                            'text-gray-500 hover:text-gray-700': activeTab !== 'personal'}"
                    class="flex-1 px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 flex items-center justify-center">
                <i class="far fa-user mr-2"></i>
                Personal
            </button>
            <button @click="activeTab = 'contact'"
                    :class="{'bg-white text-blue-600 shadow-sm': activeTab === 'contact',
                            'text-gray-500 hover:text-gray-700': activeTab !== 'contact'}"
                    class="flex-1 px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 flex items-center justify-center">
                <i class="far fa-address-card mr-2"></i>
                Contact
            </button>
            <button @click="activeTab = 'professional'"
                    :class="{'bg-white text-blue-600 shadow-sm': activeTab === 'professional',
                            'text-gray-500 hover:text-gray-700': activeTab !== 'professional'}"
                    class="flex-1 px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 flex items-center justify-center">
                <i class="far fa-building mr-2"></i>
                Work
            </button>
            <button @click="activeTab = 'social'"
                    :class="{'bg-white text-blue-600 shadow-sm': activeTab === 'social',
                            'text-gray-500 hover:text-gray-700': activeTab !== 'social'}"
                    class="flex-1 px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 flex items-center justify-center">
                <i class="far fa-share-square mr-2"></i>
                Social
            </button>
            <button @click="activeTab = 'emergency'"
                    :class="{'bg-white text-blue-600 shadow-sm': activeTab === 'emergency',
                            'text-gray-500 hover:text-gray-700': activeTab !== 'emergency'}"
                    class="flex-1 px-3 py-2 text-sm font-medium rounded-md transition-all duration-200 flex items-center justify-center">
                <i class="far fa-life-ring mr-2"></i>
                Emergency
            </button>
        </nav>

        <form wire:submit="updateProfileInformation" class="mt-6">
            <!-- Personal Information Tab -->
            <div x-show="activeTab === 'personal'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <div class="flex items-center text-sm text-gray-600 mb-4">
                        <i class="fas fa-user-circle text-blue-500 mr-2"></i>
                        <span>Your personal information helps us personalize your experience</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Bio -->
                        <div class="md:col-span-2">
                            <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
                            <div class="mt-1 relative">
                                <textarea id="bio" wire:model="state.bio" rows="2"
                                    class="block w-full text-sm border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                    placeholder="Write a short bio about yourself..."></textarea>
                                <div class="absolute bottom-2 right-2 text-xs text-gray-400">
                                    <span x-text="$wire.state.bio ? $wire.state.bio.length : 0">/500</span>
                                </div>
                            </div>
                        </div>

                        <!-- Date of Birth -->
                        <div class="relative">
                            <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                            <div class="mt-1">
                                <input type="date" wire:model="state.date_of_birth"
                                    class="block w-full text-sm border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <!-- Gender with custom select -->
                        <div class="relative">
                            <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
                            <div class="mt-1">
                                <select wire:model="state.gender"
                                    class="block w-full text-sm border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 pr-10">
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 top-6 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                        </div>

                        <!-- ID Information -->
                        <div class="relative">
                            <label for="id_type" class="block text-sm font-medium text-gray-700">ID Type</label>
                            <div class="mt-1">
                                <select wire:model="state.id_type"
                                    class="block w-full text-sm border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select ID Type</option>
                                    <option value="national_id">National ID</option>
                                    <option value="passport">Passport</option>
                                    <option value="drivers_license">Driver's License</option>
                                </select>
                            </div>
                        </div>

                        <div class="relative">
                            <label for="id_number" class="block text-sm font-medium text-gray-700">ID Number</label>
                            <div class="mt-1">
                                <input type="text" wire:model="state.id_number"
                                    class="block w-full text-sm border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information Tab -->
            <div x-show="activeTab === 'contact'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <div class="flex items-center text-sm text-gray-600 mb-4">
                        <i class="fas fa-map-marker-alt text-blue-500 mr-2"></i>
                        <span>Your contact details help us reach you when needed</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Phone -->
                        <div class="md:col-span-2">
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <div class="mt-1 flex rounded-lg shadow-sm">
                                <div class="relative flex-shrink-0">
                                    <select wire:model="state.country_code"
                                        class="block w-20 text-sm border-gray-300 rounded-l-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 border-r-0">
                                        <option value="+1">🇺🇸 +1</option>
                                        <option value="+44">🇬🇧 +44</option>
                                        <option value="+254">🇰🇪 +254</option>
                                    </select>
                                </div>
                                <input type="tel" wire:model="state.phone_number"
                                    class="flex-1 text-sm border-gray-300 rounded-r-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Phone number">
                            </div>
                        </div>

                        <!-- Address with icon -->
                        <div class="md:col-span-2 relative">
                            <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                            <div class="mt-1 relative">
                                <input type="text" wire:model="state.address"
                                    class="block w-full text-sm border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 pl-10"
                                    placeholder="Enter your address">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-home text-gray-400"></i>
                                </div>
                            </div>
                        </div>

                        <!-- City & State -->
                        <div class="relative">
                            <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                            <div class="mt-1">
                                <input type="text" wire:model="state.city"
                                    class="block w-full text-sm border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div class="relative">
                            <label for="state" class="block text-sm font-medium text-gray-700">State/Province</label>
                            <div class="mt-1">
                                <input type="text" wire:model="state.state"
                                    class="block w-full text-sm border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <!-- Country & Postal Code -->
                        <div class="relative">
                            <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                            <div class="mt-1">
                                <select wire:model="state.country"
                                    class="block w-full text-sm border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Country</option>
                                    <option value="US">United States</option>
                                    <option value="GB">United Kingdom</option>
                                    <option value="KE">Kenya</option>
                                </select>
                            </div>
                        </div>

                        <div class="relative">
                            <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal Code</label>
                            <div class="mt-1">
                                <input type="text" wire:model="state.postal_code"
                                    class="block w-full text-sm border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Professional Information Tab -->
            <div x-show="activeTab === 'professional'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <div class="flex items-center text-sm text-gray-600 mb-4">
                        <i class="fas fa-briefcase text-blue-500 mr-2"></i>
                        <span>Your professional details help build trust in the community</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="relative">
                            <label for="occupation" class="block text-sm font-medium text-gray-700">Occupation</label>
                            <div class="mt-1">
                                <input type="text" wire:model="state.occupation"
                                    class="block w-full text-sm border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div class="relative">
                            <label for="company" class="block text-sm font-medium text-gray-700">Company</label>
                            <div class="mt-1">
                                <input type="text" wire:model="state.company"
                                    class="block w-full text-sm border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div class="md:col-span-2 relative">
                            <label for="website" class="block text-sm font-medium text-gray-700">Website</label>
                            <div class="mt-1">
                                <input type="url" wire:model="state.website"
                                    class="block w-full text-sm border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 pl-10"
                                    placeholder="https://">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-globe text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Social Media Tab -->
            <div x-show="activeTab === 'social'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <div class="flex items-center text-sm text-gray-600 mb-4">
                        <i class="fas fa-share-alt text-blue-500 mr-2"></i>
                        <span>Connect with your social networks</span>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-[#1877F2] rounded-lg flex items-center justify-center transform transition-transform hover:scale-105">
                                <i class="fab fa-facebook-f text-white text-xl"></i>
                            </div>
                            <div class="flex-1 relative">
                                <input type="url" wire:model="social.facebook"
                                    class="block w-full text-sm border-gray-300 rounded-lg focus:ring-1 focus:ring-[#1877F2] focus:border-[#1877F2]"
                                    placeholder="Your Facebook profile URL">
                            </div>
                        </div>

                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-[#1DA1F2] rounded-lg flex items-center justify-center transform transition-transform hover:scale-105">
                                <i class="fab fa-twitter text-white text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <input type="url" wire:model="social.twitter"
                                    class="block w-full text-sm border-gray-300 rounded-lg focus:ring-1 focus:ring-[#1DA1F2] focus:border-[#1DA1F2]"
                                    placeholder="Your Twitter profile URL">
                            </div>
                        </div>

                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-[#0A66C2] rounded-lg flex items-center justify-center transform transition-transform hover:scale-105">
                                <i class="fab fa-linkedin-in text-white text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <input type="url" wire:model="social.linkedin"
                                    class="block w-full text-sm border-gray-300 rounded-lg focus:ring-1 focus:ring-[#0A66C2] focus:border-[#0A66C2]"
                                    placeholder="Your LinkedIn profile URL">
                            </div>
                        </div>

                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-tr from-[#F58529] via-[#DD2A7B] to-[#8134AF] rounded-lg flex items-center justify-center transform transition-transform hover:scale-105">
                                <i class="fab fa-instagram text-white text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <input type="url" wire:model="social.instagram"
                                    class="block w-full text-sm border-gray-300 rounded-lg focus:ring-1 focus:ring-[#DD2A7B] focus:border-[#DD2A7B]"
                                    placeholder="Your Instagram profile URL">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Emergency Contact Tab -->
            <div x-show="activeTab === 'emergency'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <div class="flex items-center text-sm text-gray-600 mb-4">
                        <i class="fas fa-phone-alt text-blue-500 mr-2"></i>
                        <span>Emergency contacts are only used in critical situations</span>
                    </div>

                    <div class="space-y-4">
                        <div class="relative">
                            <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700">Emergency Contact Name</label>
                            <div class="mt-1">
                                <input type="text" wire:model="state.emergency_contact_name"
                                    class="block w-full text-sm border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 pl-10"
                                    placeholder="Full name">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                            </div>
                        </div>

                        <div class="relative">
                            <label for="emergency_contact_number" class="block text-sm font-medium text-gray-700">Emergency Contact Number</label>
                            <div class="mt-1">
                                <input type="tel" wire:model="state.emergency_contact_number"
                                    class="block w-full text-sm border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 pl-10"
                                    placeholder="Phone number">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-phone text-gray-400"></i>
                                </div>
                            </div>
                        </div>

                        <div class="relative">
                            <label for="emergency_contact_relationship" class="block text-sm font-medium text-gray-700">Relationship</label>
                            <div class="mt-1">
                                <input type="text" wire:model="state.emergency_contact_relationship"
                                    class="block w-full text-sm border-gray-300 rounded-lg focus:ring-1 focus:ring-blue-500 focus:border-blue-500 pl-10"
                                    placeholder="e.g. Parent, Spouse, Sibling">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-heart text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex items-center justify-between border-t pt-5">
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-shield-alt text-blue-500 mr-2"></i>
                    <span>Your information is securely stored</span>
                </div>

                <div class="flex items-center space-x-4">
                    <span x-data="{ shown: false }"
                          x-show="shown"
                          x-transition:enter="transition ease-out duration-300"
                          x-transition:enter-start="opacity-0 transform translate-x-2"
                          x-transition:enter-end="opacity-100 transform translate-x-0"
                          x-init="@this.on('profile-updated', () => { shown = true; setTimeout(() => { shown = false }, 2000); })"
                          class="text-sm text-green-600 flex items-center">
                        <i class="fas fa-check-circle mr-1"></i> Changes saved
                    </span>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transform transition-transform hover:scale-105">
                        <span wire:loading.remove wire:target="updateProfileInformation">
                            <i class="fas fa-save mr-2"></i> Save Changes
                        </span>
                        <span wire:loading wire:target="updateProfileInformation">
                            <i class="fas fa-spinner fa-spin mr-2"></i> Saving...
                        </span>
                    </button>
                </div>
            </div>
        </form>

        <!-- Add this debug section at the bottom of the form, before the closing </form> tag -->
        @if(app()->environment('local'))
        <div class="mt-6 p-4 bg-gray-100 rounded-lg">
            <h3 class="text-sm font-medium text-gray-700 mb-2">Debug Information</h3>
            <div class="text-xs text-gray-600">
                <p>Form State: <pre>{{ json_encode($state, JSON_PRETTY_PRINT) }}</pre></p>
                <p>Social Links: <pre>{{ json_encode($social, JSON_PRETTY_PRINT) }}</pre></p>
            </div>
        </div>
        @endif
    </div>
</div>
