@extends('errors.layout')

@section('title', 'Server Error')

@section('icon')
fa-server
@endsection

@section('icon-bg')
bg-orange-100
@endsection

@section('icon-color')
text-orange-600
@endsection

@section('message')
Internal Server Error
@endsection

@section('description')
We're experiencing some technical difficulties. Our team has been notified and is working to fix the issue. Please try again later.
@endsection

@section('additional-content')
<div class="px-8 pb-8">
    <div class="bg-orange-50 rounded-xl p-4 border border-orange-100">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-orange-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-orange-800">
                    What you can try:
                </h3>
                <div class="mt-2 text-sm text-orange-700">
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Refresh the page</li>
                        <li>Clear your browser cache</li>
                        <li>Try again in a few minutes</li>
                        <li>Contact support if the problem persists</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
