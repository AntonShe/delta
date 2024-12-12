<template>
    <div class="container profile">
        <h1 v-if="isGuest !==null && isGuest" class="profile__title">
            Избранное
        </h1>
        <h1 v-else-if="isGuest !==null && !isGuest" v-show="isShowNavigation" class="profile__title">
            Мой кабинет
        </h1>
        <div v-if="isGuest !==null && isGuest" class="profile__wrapper">
            <div class="profile__main-guest">
                <component :is="getComponent" :need-title="!isGuest"/>
            </div>
        </div>
        <div v-else-if="isGuest !==null && !isGuest" class="profile__wrapper">
            <div v-show="isShowNavigation" class="profile__navigation">
                <div class="profile__card">
                    <user-badge :config="badge"/>
                </div>
                <div class="profile__menu">
                    <div class="profile__menu-list">
                        <profile-menu :config="menu"/>
                    </div>
                    <div class="profile__menu-button">
                        <div class="profile__button-icon">
                            <base-svg-store icon="logout"/>
                        </div>
                        <a class="profile__button-link" href="/logout">Выход</a>
                    </div>
                </div>
            </div>
            <div class="profile__main-block">
                <component :is="getComponent"/>
            </div>
        </div>
    </div>
</template>

<script>
import UserBadge from "./UserBadge"
import axios from "axios";
import ProfileMenu from "./ProfileMenu"
import {
    ProfileInfo,
    ProfileOrders,
    ProfileBalance,
    ProfileFavourites,
} from "./content"
import OrderDetail from "./content/order/OrderDetail"
import BaseSvgStore from "../base/BaseSvgStore";

export default {
    name: "Profile",
    components: {
        BaseSvgStore,
        ProfileMenu,
        UserBadge
    },
	data() {
        return {
            badge: {
                phone: '',
                email: '',
                fio: ''
            },
            isGuest: null,
			page: null,
			components: {
                info: ProfileInfo,
                orders: ProfileOrders,
                favourite: ProfileFavourites,
                balance: ProfileBalance,
                detail: OrderDetail,
			},
			menu: {
                prefix: 'profile',
				default: 'info',
				items: [
                    {
                        path: 'info',
                        icon: 'user',
                        title: 'Личные данные',
                    },
                    {
                        path: 'orders',
                        icon: 'shopping-bag',
                        title: 'Заказы'
                    },
                    // {
                    //     path: 'balance',
                    //     icon: 'database',
                    //     title: 'Баланс'
                    // },
                    {
                        path: 'favourite',
                        icon: 'heart',
                        title: 'Избранное'
                    },
				]
			},
			isMobile: (window.innerWidth < 760),
        }
	},
	beforeMount() {
        this.getProfile();
        this.page = this.$route.params.page;
        window.addEventListener('resize', this.onResize);
    },
    watch: {
        $route(to, from) {
            this.page = to.params.page;
        },
	},
	methods: {
        getProfile() {
            axios.get('/user/get-user-badge')
                .then((result) => {
                    this.badge = {...result.data.badge, city: result.data.city};
                    this.isGuest = result.data.isGuest
                });
		},
        onResize() {
            this.isMobile = (window.innerWidth < 760);
		},
	},
	computed: {
        getProfileInfo() {
            return (this.profile) ? this.profile.info : null;
		},
        isShowNavigation() {
            return (this.isMobile && this.page.length !== 0) ? false :true;
        },
        getComponent() {
            let component = null;

            if (this.isMobile && this.page.length === 0) {
                return component;
			}

            if (this.$route.params.orderNumber) {
                return OrderDetail;
			}

            switch(this.page) {
                case ('orders'):
                    component = ProfileOrders;
                    break;
                case ('favourite'):
                    component =  ProfileFavourites;
                    break;
                case ('balance'):
                    component =  ProfileBalance;
                    break;
                case ('info'):
                    component =  ProfileInfo;
                    break;
				default:
                    component =  ProfileInfo;
                    break;
			}

            return component;
		}
	}
}
</script>

<style lang="scss">
.profile {
    padding-top: 20px;
    padding-bottom: 50px;

    @media (max-width: $screen-xl) {
        padding-top: 10px;
        padding-bottom: 40px;
    }

    @media (max-width: $screen-md) {
        padding-top: 76px;
    }

    &__title {
        margin-bottom: 20px;
        @include _typography-ext(fn, 30, 36, 600, ls, $Zinc-900);

        @media (max-width: $screen-xl) {
            @include _typography-ext(fn, 24, 32, 600, ls, $Zinc-900);
        }

        @media (max-width: $screen-md) {
            @include _typography-ext(fn, 20, 28, 600, ls, $Zinc-900);
        }
    }

    &__wrapper {
        display: flex;
    }

    &__navigation {
        width: 252px;
        margin-right: 20px;

        @media (max-width: $screen-xl-l) {
            width: 230px;
        }

        @media (max-width: $screen-md) {
            width: 100%;
            margin-right: 0;
        }
    }

    &__main-block {
        width: calc(100% - 272px);

        &:empty {
            width: unset;
        }

        @media (max-width: $screen-xl-l) {
            width: calc(100% - 250px);
        }

        @media (max-width: $screen-md) {
            width: 100%;
        }
    }

    &__menu {
        width: 100%;
        padding: 10px;
        @include _border-radius(8px);
        background-color: $Zinc-100;

        &-button {
            display: flex;
            align-items: center;
            justify-content: end;

            .profile__button {
                cursor: pointer;
                &-icon {
                    margin-right: 8px;
                    width: 20px;
                    height: 20px;

                    svg path {
                        stroke: $Zinc-500;
                    }
                }

                &-link {
                    @include _typography-ext(fn, 14, 20, 600, ls, $Zinc-500);
                }

                &:hover {
                    .profile__button-icon svg path {
                        stroke: $Red-400;
                    }

                    .profile__button-link {
                        color: $Red-400;
                    }
                }
            }
        }
    }
}

.profile__main-guest {
    width: 100%;
}
</style>