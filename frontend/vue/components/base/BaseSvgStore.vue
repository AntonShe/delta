<template>
    <div class="svg-store" :class="getClass">
        <svg
            :width="params.width"
            :height="params.height"
            :viewBox="params.viewBox"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            v-html="paths"
        >
        </svg>
    </div>
</template>


<script>
export default {
    name: "BaseSvgStore",
    props: {
        icon: String,
        width: String,
        height: String,
        color: String
    },
    data() {
        return {
            paths: null,
            params: {
                width: null,
                height: null,
                viewBox: null,
            }
        }
    },
    methods: {
        getPath() {
            return require('../../../web/img/icons/' + this.icon + '.svg');
        },
        async setIcon() {
            let svgText = null;

            await fetch(this.getPath())
                .then((response) => response.text())
                .then(text => {
                    svgText = text;
                });

            this.svgAsArray = svgText.split('\n').filter(element => element.length > 0);

            this.paths = this.svgAsArray.slice(1, (this.svgAsArray.length - 1)).join('\n');

            this.setParams();
        },
        setParams() {
            this.setAttributes();
        },
        setAttributes() {
            let firstRow = this.svgAsArray[0].split(' ');
            firstRow = firstRow.slice(1, (firstRow.length - 1));

            let width = this.getAttributeValue(firstRow, 'width');
            let height = this.getAttributeValue(firstRow, 'height');

            this.params.width = (this.width) ? this.width : width;
            this.params.height = (this.height) ? this.height : height;

            this.params.viewBox = `0 0 ${width} ${height}`;
        },
        getAttributeValue(row, attributeName) {
            row = row.filter(element => element.indexOf(attributeName) === 0);
            let attribute = row[0].replaceAll('"', '').split('=');

            return attribute[1];
        },
    },
    computed: {
        getClass() {
            return {

            }
        }
    },
    mounted() {
        if (this.icon) {
            this.setIcon();
        }
    }
}
</script>

<style lang="scss">

</style>