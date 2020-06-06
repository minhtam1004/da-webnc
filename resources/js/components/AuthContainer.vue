<template>
    <div class="container">
        <router-view></router-view>
    </div>
</template>

<script>
export default {
    data() {
        return {};
    }
};
</script>
<style scoped>
.container {
    display: flex;
  justify-content: center;
  height: 100%;
    background-image: linear-gradient(
        to right top,
        #051937,
        #004d7a,
        #008793,
        #00bf72,
        #a8eb12
    );
    background-size: cover;
  background-repeat: no-repeat;
  background-position: center;
  min-width: 100%;
    
}
</style>
