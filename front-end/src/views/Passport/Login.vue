<template>
    <div class="login-box">
        <div class="login-area">
            <div class="login-form" @keyup.enter="login">
                <div class="form-item">
                    <div class="lable">用户名</div>
                    <div class="input">
                        <input type="text" placeholder="用户名" v-model="userform.username" @focus="focusBT"
                               @blur="blurBT"/>
                    </div>
                </div>
                <div class="form-item">
                    <div class="lable">密码</div>
                    <div class="input">
                        <input type="password" placeholder="密码" v-model="userform.password" @focus="focusBT"
                               @blur="blurBT"/>
                    </div>
                </div>
                <div class="form-foot">
                    <div class="login-btn" @click="login">登录</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
    import useController from "@/lib/useController";
    import LoginController from "@/controller/LoginController";

    export default useController(new LoginController());
</script>


<style lang="less" scoped>
    .login-box {
        position: relative;
        height: 100%;
        background: url("~@/assets/2zrdI1g.jpg") no-repeat 50%;
        background-size: cover;

        .login-area {
            position: absolute;
            top: 50%;
            left: 50%;
            margin-left: -190px;
            margin-top: -270px;
            border-radius: 4px;
            -moz-border-radius: 4px;
            -webkit-border-radius: 4px;
            background-color: #fff;
            width: 400px;
            height: 540px;
            box-shadow: 0 2px 10px #999;
            -moz-box-shadow: #999 0 2px 10px;
            -webkit-box-shadow: #999 0 2px 10px;
            display: flex;
            justify-content: center;
            align-items: center;

            .login-form {
                position: relative;
                width: 100%;
                margin: 0 42px;

                .form-item {
                    width: 100%;
                    font-size: 14px;
                    border-bottom: 1px solid #F1F1F1;

                    &:after {
                        content: "";
                        clear: both;
                        display: table;
                    }

                    .lable {
                        width: 80px;
                        float: left;
                        padding-top: 10px;
                        padding-bottom: 10px;
                        /*padding-right: 10px;*/
                        /*text-align: right;*/
                    }

                    .input {
                        float: left;
                        padding: 0;

                        input {
                            border: none;
                            padding-top: 13px;
                            padding-bottom: 10px;

                            &:focus {
                                outline: 0;
                                background-color: #FFFFFF;
                            }
                        }
                    }
                }

                .form-foot {
                    margin: 15px 0;
                }

                .login-btn {
                    color: #ffffff;
                    border-radius: 2px;
                    background-color: #07C160;
                    width: 100%;
                    padding: 7px 0;
                    text-align: center;
                    cursor: pointer;

                    &:hover {
                        background-color: #129611;
                    }
                }
            }
        }
    }

    /*移动端*/
    @media only screen and (min-width: 320px) and (max-width: 768px) {
        .login-box {
            display: flex;
            align-items: center;

            .login-area {
                width: 90%;
                height: 320px;
                top: unset;
                bottom: unset;
                margin-top: 0;
                margin-left: 0;
                left: 5%;
                right: 5%;
            }
        }
    }
</style>
