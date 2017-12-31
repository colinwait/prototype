<template>
    <div>
        <h1>Hello Vue</h1>
        <p class="hello">{{ msg }}</p>
    </div>
</template>

<script>
    export default {
       data(){
           return {msg : 'Im in vue'}
       }
    }
</script>

<style>
    .hello {
        font-size: 2em;
        color: green;
    }
</style>