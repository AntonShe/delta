<template>
	<div class="user-badge">
        <div class="user-badge__avatar avatar">
            <img class="user-badge__icon" src="../../../web/img/profile-capybara.svg" />
        </div>
		<div class="user-badge__info">
			<div class="user-badge__info-fio">
				{{ fio }}
			</div>
			<div class="user-badge__info-phone">
                <span v-if="isPhone">{{ phone }}</span>
                <span v-else-if="isEmail">{{ email }}</span>
			</div>
		</div>
	</div>
    <div v-if="city" class="user-badge__city js-user-badge-city">{{ city }}</div>
</template>

<script>
export default {
    name: "UserBadge",
	props: {
        config: {
            type: Object,
            required: true
        }
	},
    data() {
        return {
            phone: this.config.phone,
            email: this.config.email,
            fio: this.config.fio,
            city: this.config.city || 'Москва'
        }
    },
    watch: {
        config: {
            deep: true,
            handler(newValue) {
                this.phone = newValue.phone
                this.email = newValue.email
                this.fio = newValue.fio
            }
        }
    },
	computed: {
		isPhone() {
            return !_.isEmpty(this.phone);
		},
        isEmail() {
            return !_.isEmpty(this.email);
        }
	}
}
</script>

<style lang="scss" scoped>
.user-badge {
    width: 100%;
    padding: 6px 10px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    border-radius: 8px;
    background-color: $Zinc-100;

    @media (min-width: $tablet-width) {
        margin-bottom: 4px;
        padding: 16px 10px;
    }

    &__avatar {
        margin-right: 10px;
        padding: 6px;
    }

    &__info {
        &-fio {
            margin-bottom: 2px;
            @include _typography-ext(fn, 16, 26, 600, ls, $Zinc-900);
        }

        &-phone {
            @include _typography-ext(fn, 14, 20, 400, ls, $Zinc-900);
        }
    }
}

.avatar {
    display: flex;
    flex-wrap: nowrap;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    text-align: center;
    border-radius: 100%;
    background-color: $white;
}

.user-badge__icon {
    width: 48px;
    height: 48px;
}

.user-badge__city {
    @include _typography-ext(fn, 16, 26, 600, ls, $Zinc-900);
    margin-bottom: 10px;
    padding: 10px 10px 10px 54px;
    border-radius: 8px;
    background: $Zinc-100 get-icon('location', $Zinc-900) 24px 50% no-repeat;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;

    @media (min-width: $tablet-width) {
        margin-bottom: 4px;
    }
}
</style>