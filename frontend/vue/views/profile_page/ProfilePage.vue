<template>
	<div class="bg-body">
		<Profile />
	</div>
</template>

<script>
import Profile from "../../components/profile/Profile"

export default {
    name: "ProfilePage",
    components: {
        Profile
    }
}
</script>

<style lang="scss" scoped>

.bg-body {
    background-color: #FAFAFA;
}
</style>