<template>
    <div v-for="(item, index) in config.items"
        :key="index"
         class="profile-menu"
    >
        <router-link :to="getFullPath(item.path)">
            <div class="profile-menu__item" :class="{'active': isActive(item.path)}">
                <div class="profile-menu__item-icon">
                    <BaseSvgStore :icon="item.icon"/>
                </div>
                <div class="profile-menu__item-title">
                    {{ item.title }}
                </div>
                <div v-if="$helpers.isMobileScreen()" class="profile-menu__item-arrow">
                    <BaseSvgStore icon="chevron-right" />
                </div>
            </div>
        </router-link>
    </div>
</template>

<script>

export default {
    name: "ProfileMenu",
	props: {
        config: Object,
	},
	methods: {
        getFullPath(path) {
            let fullPath = '/' + this.config.prefix;

            if (path.length !== 0) {
                fullPath += '/' + path;
            }

            return fullPath;
        },
        isActive(path) {
            if (this.$route.params.page.length === 0) {
                return this.config.default === path;
			}

			return this.$route.params.page === path;
        }
	}
}
</script>

<style lang="scss">
.profile-menu {
    &__item {
        padding: 9px 16px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        @include _border-radius(8px);
        background-color: $white;

        &:hover {
            .profile-menu__item-title {
                color: $Red-400;
            }

            path {
                stroke: $Red-400;
            }
        }

        &.active {
            path {
                stroke: $Red-400;
            }

            .profile-menu__item-title {
                color: $Red-400;
            }
        }

        svg {
            width: 20px;
            height: 20px;

            path {
                stroke: $Zinc-700;

                @media (min-width: $tablet-width) {
                    stroke: $Zinc-700;
                }
            }
        }

        &-icon {
            margin-right: 8px;
            width: 20px;
            height: 20px;
        }

        &-arrow {
            width: 20px;
            height: 20px;
        }

        &-title {
            margin-right: auto;
            @include _typography-ext(fn, 16, 26, 600, ls, $Zinc-900);

            @media (min-width: $tablet-width) {
                color: $Zinc-700;
            }
        }
    }
}
</style>