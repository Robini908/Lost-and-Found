<div id="chat" class="flex flex-col h-64 p-4 bg-gray-100 rounded-lg">
    <div id="messages" class="flex-1 mb-4 p-4 bg-white rounded shadow overflow-y-scroll">
        @foreach($messages as $message)
            <p class="mb-2"><strong>{{ $message['user'] }}:</strong> {{ $message['text'] }}</p>
        @endforeach
    </div>
    <div class="flex">
        <input type="text" wire:model="message" placeholder="Type a message..." class="flex-1 p-2 border rounded-l-md">
        <button wire:click="sendMessage" class="p-2 bg-blue-500 text-white rounded-r-md">Send</button>
    </div>
</div>
