/**
 * 前置引入area组件
 */
var commonVideo = (function() {
	const html =
		`<video controls="controls" autoplay="autoplay" loop="loop" :poster='img' :src="url" id="source">
					<source :src="url" type="video/mp4">
					</source>
				</video>`

	return {
		data: function() {
			return {}
		},
		template: html,
		props: {
			img: {
				type: String,
				default () {
					return ''
				}
			},
			url: {
				type: String,
				default () {
					return ''
				}
			},
		},
		created() {},
		methods: {},
	}
}());
