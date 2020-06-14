<template>
  <!-- Default form register -->
  <div class="container-login">
  <form>
    <p class="h4 text-center mb-4">Sign up</p>
    <label for="defaultFormRegisterNameEx" class="grey-text">First name</label>
    <input type="text" id="defaultFormRegisterNameEx" class="form-control"/>
    <br/>
    <label for="defaultFormRegisterNameEx" class="grey-text">Last name</label>
    <input type="text" id="defaultFormRegisterNameEx" class="form-control"/>
    <br/>
    <label for="defaultFormRegisterEmailEx" class="grey-text">Email Address</label>
    <input type="email" id="defaultFormRegisterEmailEx" class="form-control"/>
    <br/>
    <label for="defaultFormRegisterPhoneEx" class="grey-text">Phone Number</label>
    <input type="phone" id="defaultFormRegisterPhoneEx" class="form-control"/>
    <div class="text-center mt-4">
      <button class="btn btn-unique" type="submit">Register</button>
    </div>
  </form>
  </div>
  <!-- Default form register -->
</template>
<style scoped>
.container-login {
  width: 50%;
  margin: auto;
}
</style>