<template x-if="toast.type==='debug'">
    <i class="fas fa-bug text-gray-700 text-2xl animate-spin"></i>
</template>

<template x-if="toast.type==='info'">
    <i class="fas fa-info-circle text-blue-700 text-2xl animate-bounce"></i>
</template>

<template x-if="toast.type==='success'">
    <i class="fas fa-check-circle text-green-700 text-2xl animate-pulse"></i>
</template>

<template x-if="toast.type==='warning'">
    <i class="fas fa-exclamation-triangle text-yellow-700 text-2xl animate-ping"></i>
</template>

<template x-if="toast.type==='danger'">
    <i class="fas fa-times-circle text-red-700 text-2xl animate-wiggle"></i>
</template>
