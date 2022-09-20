<template>
    <div class="container">
        <div class="first-nav">
            <div class="avatar">
                <img :src="userinfo?.avatar"/>
            </div>
            <NavItem :item="menu.message"/>
            <NavItem :item="menu.contact"/>
            <NavItem :item="menu.me"/>
            <div class="setting">
                <div class="setting-menu" v-if="showSettingMenu">
                    <div class="setting-item" @click.stop="()=>{}">设置</div>
                    <div class="setting-item" @click.stop="logout">退出登录</div>
                </div>
                <NavItem :item="menu.setting"/>
            </div>
        </div>
        <div class="second-nav">
            <router-view name="second-nav"/>
        </div>
        <div class="third-nav" v-if="!is_mobile">
            <router-view name="third-nav"/>
        </div>
    </div>
</template>

<script lang="ts">
    import MainController from "@/controller/MainController";
    import useController from "@/lib/useController";

    export default useController(new MainController());
</script>
<style lang="less" scoped>
    .container {
        height: 100%;
    }

    .first-nav {
        position: relative;
        width: 60px;
        height: 100%;
        background-color: #2B2C2E;
        float: left;

        .avatar {
            width: 35px;
            height: 35px;
            margin: 0 auto;
            box-sizing: content-box;
            padding-top: 20px;
            cursor: pointer;
            /*margin-top: 20px;*/

            img {
                width: 100%;
                height: 100%;
                border-radius: 2px;
            }
        }


        .setting {
            position: absolute;
            width: 100%;
            bottom: 0;

            .setting-menu {
                position: absolute;
                left: 100%;
                min-width: 137px;
                height: auto;
                bottom: 25px;
                z-index: 10;
                background-color: #2B2C2E;
                border-radius: 0 2px 2px 0;
                //box-shadow: 0 0 5px #808080;

                .setting-item {
                    text-align: center;
                    padding: 10px;
                    width: 100%;
                    color: #949394;
                    cursor: pointer;
                    &:hover {
                        background-color: #404040;
                    }
                }
            }
        }
    }

    .second-nav {
        position: relative;
        float: left;
        width: 250px;
        height: 100%;
        background-color: #EFECE9;
        background-image: linear-gradient(180deg, #EFECEA, #E7E5E4);
    }

    .third-nav {
        position: relative;
        float: left;
        width: calc(100% - calc(250px + 60px));
        height: 100%;
        background-color: #F5F5F5;
    }

    /*移动端*/
    @media only screen and (min-width: 320px) and (max-width: 768px) {
        .first-nav {
            position: absolute;
            width: 100%;
            height: 60px;
            bottom: 0;
            display: flex;
            justify-content: center;
            align-items: center;

            .avatar {
                display: none;
            }

            .setting {
                display: none;
            }
        }

        .second-nav {
            position: absolute;
            width: 100%;
            height: calc(100% - 60px);
            bottom: 60px;
            /*background-color: #FFFFFF;*/
            /*background-image: none;*/
        }

        .third-nav {
            display: none;
        }
    }
</style>
