<template>
    <div class="wrapper">
        <div class="return-home">
            <router-link :to="{name: 'home'}"><i class="fas fa-home"></i> GO BACK HOME</router-link>
        </div>
        <br><br>
        
        <div class="form-container lg-container" style="background:none;">
            <div style="padding:1rem;" class="form-img-div">
                <img loading="lazy" :src="asset('assets/illustrations/team-spirit-animate.svg')" alt="">
                <div class="div-header">
                    <h3 class="div-meta">Welcome Back! Login to connect with other awesome users</h3>
                    <span class="hr-line"></span>
                </div>
            </div>
            <form @submit.prevent="login">
                <input type="hidden" name="csrf_token" :value="csrf()">
                <div class="input-div">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input required type="text" name="email" value="" @input="validateForm($event)" v-model="form.email">
                    <div class="form-validation"></div>
                </div>
                <div class="input-div">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <input required :type="!showPassword? 'password': 'text'" name="password" id="password" v-model="form.password">
                    <button type="button" id="show-password" @click="showPassword = !showPassword"><span v-if="!showPassword"><i class="fas fa-eye"></i></span><span v-else><i class="fas fa-eye-slash"></i></span></button>
                </div>
                <div>
                    <input type="checkbox" name="remember" id="remember" >
                    <label for="remember">
                        Remember Me
                    </label>
                </div>
                <button type="submit" name="login-btn">Login</button>
                <div>
                    <p><router-link :to="{name: 'register'}" href="">Don't have an account yet?</router-link></p>
                    <p><router-link :to="{name: 'password', params:{page: 'email'}}">Forgot your password?</router-link></p>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            form: {
                email: "",
                password: "",
                device_name: "fuoye360_webapp"
            },
            showPassword: false
        }
    },
    mounted(){
        $('.form-container input, .form-container textarea').on('focusin', function() {
            $(this).parent().find('label').addClass('active');
        });
        $('.form-container input, .form-container textarea').on('focusout', function() {
            if (!this.value) {
                $(this).parent().find('label').removeClass('active');
            }
        });
        $("#uName").keyup(function() {
            this.value = this.value.replace(/ /g, "_")
        });
        $('.form-container label').click(function() {
            $(this).parent().find('input').focus();
        });
    },
    created(){
        this.currentPage = '<i class="fas fa-lock"> Login';
    },
    methods: {
        validateForm(e){
            let formInput,format;
            formInput = e.target.name;
            if (formInput == 'username') {
                format = /[ `!@#$%^&*()+\-=\[\]{};':"\\|,.<>\/?~]/;
                // console.log(format);
                if (e.target.value.substr(-1) == "_" || e.target.value.substr(-1) == " " || e.target.value.substr(1) == " " || e.target.value.substr(0) == "_" || format.test(e.target.value)) {
                    $(e.target.parentElement).children('.form-validation').removeClass('i-invalid');
                    $(e.target.parentElement).children('.form-validation').removeClass('valid');
                    $(e.target.parentElement).children('.form-validation').addClass('invalid').html("&times;").show();
                } else if (e.target.value == '') {
                    $(e.target.parentElement).children('.form-validation').removeClass('valid').addClass('invalid i-invalid').html("<i class=\"fas fa-info-circle\"></i>").show();
                } else {
                    $(e.target.parentElement).children('.form-validation').removeClass('invalid');
                    $(e.target.parentElement).children('.form-validation').removeClass('i-invalid');
                    $(e.target.parentElement).children('.form-validation').addClass('valid').html("&check;").show();
                }
            } else if (formInput == "mobile_number") {
                format = /^\d+$/;
                if (format.test(e.target.value) && e.target.value.length == 11) {
                    $(e.target.parentElement).children('.form-validation').removeClass('invalid');
                    $(e.target.parentElement).children('.form-validation').removeClass('i-invalid');
                    $(e.target.parentElement).children('.form-validation').addClass('valid').html("&check;").show();
                } else if (e.target.value == '') {
                    $(e.target.parentElement).children('.form-validation').removeClass('valid').addClass('invalid').html("<i class=\"fas fa-info-circle\"></i>").show();
                } else {
                    $(e.target.parentElement).children('.form-validation').removeClass('i-invalid');
                    $(e.target.parentElement).children('.form-validation').removeClass('valid');
                    $(e.target.parentElement).children('.form-validation').addClass('invalid').html("&times;").show();
                }
            } else if (formInput == "email") {
                format = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                if (format.test(e.target.value)) {
                    $(e.target.parentElement).children('.form-validation').removeClass('invalid');
                    $(e.target.parentElement).children('.form-validation').removeClass('i-invalid');
                    $(e.target.parentElement).children('.form-validation').addClass('valid').html("&check;").show();
                } else if (e.target.value == '') {
                    $(e.target.parentElement).children('.form-validation').removeClass('valid').addClass('invalid').html("<i class=\"fas fa-info-circle\"></i>").show();
                } else {
                    $(e.target.parentElement).children('.form-validation').removeClass('i-invalid');
                    $(e.target.parentElement).children('.form-validation').removeClass('valid');
                    $(e.target.parentElement).children('.form-validation').addClass('invalid').html("&times;").show();
                }
            } else if (formInput == "location") {
                format = /[ `!@#$%^&*()+_\-=\[\]{};':"\\|.<>\/?~]/;
                if (e.target.value.substr(-1) == "_" || e.target.value.substr(-1) == " " || e.target.value.substr(1) == " " || e.target.value.substr(0) == "_" || format.test(e.target.value)) {
                    $(e.target.parentElement).children('.form-validation').removeClass('i-invalid');
                    $(e.target.parentElement).children('.form-validation').removeClass('valid');
                    $(e.target.parentElement).children('.form-validation').addClass('invalid').html("&times;").show();
                } else if (e.target.value == '') {
                    $(e.target.parentElement).children('.form-validation').removeClass('valid').addClass('invalid').html("<i class=\"fas fa-info-circle\"></i>").show();
                } else {
                    $(e.target.parentElement).children('.form-validation').removeClass('invalid');
                    $(e.target.parentElement).children('.form-validation').removeClass('i-invalid');
                    $(e.target.parentElement).children('.form-validation').addClass('valid').html("&check;").show();
                }
            } else if (formInput == "password") {
                console.log(e.target.value);
                if (e.target.value.length < 8) {
                    $(e.target.parentElement).children('.form-validation').removeClass('i-invalid');
                    $(e.target.parentElement).children('.form-validation').removeClass('valid');
                    $(e.target.parentElement).children('.form-validation').addClass('invalid').html("&times;").show();
                } else if (e.target.value == '') {
                    $(e.target.parentElement).children('.form-validation').removeClass('valid').addClass('invalid').html("<i class=\"fas fa-info-circle\"></i>").show();
                } else {
                    $(e.target.parentElement).children('.form-validation').removeClass('invalid');
                    $(e.target.parentElement).children('.form-validation').removeClass('i-invalid');
                    $(e.target.parentElement).children('.form-validation').addClass('valid').html("&check;").show();
                }
            } else if (formInput == "password_confirmation") {
                if (e.target.value == $("#password").val()) {
                    $(e.target.parentElement).children('.form-validation').removeClass('invalid');
                    $(e.target.parentElement).children('.form-validation').removeClass('i-invalid');
                    $(e.target.parentElement).children('.form-validation').addClass('valid').html("&check;").show();
                } else if (e.target.value == '') {
                    $(e.target.parentElement).children('.form-validation').addClass('invalid').html("<i class=\"fas fa-info-circle\"></i>").show();
                } else {
                    $(e.target.parentElement).children('.form-validation').removeClass('i-invalid');
                    $(e.target.parentElement).children('.form-validation').removeClass('valid');
                    $(e.target.parentElement).children('.form-validation').addClass('invalid').html("&times;").show();
                }
            }
        },
        login(){
            $("button[type=submit]").css('opacity', '.75').attr('disabled', true).html('<span class="loading-circle "></span> LOGGING IN...');
            axios.get('/sanctum/csrf-cookie').then(response => {
                axios.post('/api/login', this.form).then(()=>{
                    this.$router.push({name: 'shop'});
                    this.$emit('authentication', true);
                }).catch((err)=>{
                    $("button[type=submit]").css('opacity', '1').attr('disabled', false).html('LOGIN');
                    this.$emit('alertNotification', err.response.data.errors);
                });
            });
        }
    },
}
</script>