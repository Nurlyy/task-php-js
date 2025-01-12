<template>
    <div class="px-32 pt-12 min-h-screen h-full w-full bg-slate-700">
        <header>
        </header>
        <main>
            <slot />
        </main>
    </div>
</template>
  
<script>
export default {
    name: 'DefaultLayout',
};
</script>