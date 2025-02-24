<!-- filepath: /c:/my-projects/lost-found/resources/views/livewire/date-picker.blade.php -->
<div wire:ignore>
    <input type="text" id="datepicker" class="form-input" placeholder="Select Date" />
</div>

@script
<script>
    document.addEventListener('livewire:load', function () {
        flatpickr("#datepicker", {
            dateFormat: "Y-m-d",
            onChange: function(selectedDates, dateStr, instance) {
                @this.set('date', dateStr);
            }
        });
    });
</script>
@endscript
