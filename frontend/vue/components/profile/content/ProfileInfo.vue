<template>
    <div>
        <div v-show="isMobile">
            <profile-title title="Личные данные" />
        </div>
		<div v-if="isIsset">
            <div class="profile-info__tabs">
                <div
                    v-if="!isLegal || canSwitchToLegal"
                    :class="['profile-info__tab', {'active': activeTab === 1}, {'disabled': !canSwitchToLegal}]"
                    @click="setActive(1)"
                >
                    Физическое лицо
                </div>
                <div
                    v-if="isLegal || canSwitchToLegal"
                    :class="['profile-info__tab', {'active': activeTab === 2}, {'disabled': !canSwitchToLegal}]"
                    @click="setActive(2)"
                >
                    Юридическое лицо
				</div>
			</div>
			<div>
				<div v-if="activeTab === 1">
                    <Physical />
				</div>
				<div v-if="activeTab === 2">
                    <Legal />
				</div>
			</div>
		</div>

    </div>
</template>

<script>
import Physical from "./info/Physical"
import Legal from "./info/Legal"
import ProfileTitle from "./ProfileTitle"
import { mapActions, mapState } from 'vuex'

export default {
    name: "ProfileInfo",
    components: {
        ProfileTitle,
        Legal,
        Physical
    },
    data() {
        return {
            activeTab: 0
        }
    },
    async beforeMount() {
        await this.getUserInfo()
        this.activeTab = this.userActiveTab
    },
    methods: {
        ...mapActions({
            getUserInfo: 'user/getUserInfo'
        }),
        isMobile() {
            return window.innerWidth < 760
        },
		setActive(tab) {
            if (this.userInfo.canSwitchToLegal) {
                this.activeTab = tab
            }
		}
	},
	computed: {
        ...mapState({
            userInfo: state => state.user.userInfo,
            userActiveTab: state => state.user.activeTab
        }),        
        isIsset() {
            return !_.isEmpty(this.userInfo)
        },
        isLegal() {
            return this.isIsset ? this.userInfo.isLegal : null
		},
        canSwitchToLegal() {
            if (!this.isIsset) return true

            return this.userInfo.canSwitchToLegal
        }
	}
}
</script>

<style lang="scss" scoped>
.profile-info {
    &__tabs {
        width: 100%;
        max-width: 520px;
        display: flex;
        margin-bottom: 20px;
    }

    &__tab {
        width: 50%;
        text-align: center;
        @include _typography-ext(fn, 16, 26, 600, ls, $Zinc-400);
        border-bottom: 2px solid #E4E4E7;
        cursor: pointer;
        transition: border-bottom-color 0.2s ease-in-out;

        &.active {
            color: $Zinc-700;
            border-color: $Zinc-700;
        }
    }
}

.disabled {
    cursor: default;
}
</style>