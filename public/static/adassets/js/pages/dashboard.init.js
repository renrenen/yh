$(function() {
	$('[data-plugin="knob"]').knob()
});

function tb(data,arr){
var	options = {
		chart: {
			height: 350,
			type: "area",
			toolbar: {
				show: !1
			}
		},
		colors: ["#3051d3", "#e4cc37"],
		dataLabels: {
			enabled: !1
		},
		series: [{
			name: "昨天",
			data: [data.lastday[data.time[0]], data.lastday[data.time[1]], data.lastday[data.time[2]],  data.lastday[data.time[3]],  data.lastday[data.time[4]],  data.lastday[data.time[5]],  data.lastday[data.time[6]],  data.lastday[data.time[7]],  data.lastday[data.time[8]],  data.lastday[data.time[9]],  data.lastday[data.time[10]],  data.lastday[data.time[11]]]
		}, {
			name: "今天",
			data: [data.today[data.time[0]], data.today[data.time[1]], data.today[data.time[2]], data.today[data.time[3]], data.today[data.time[4]], data.today[data.time[5]], data.today[data.time[6]], data.today[data.time[7]], data.today[data.time[8]], data.today[data.time[9]], data.today[data.time[10]], data.today[data.time[11]]]
		}],
		grid: {
			yaxis: {
				lines: {
					show: !1
				}
			}
		},
		stroke: {
			width: 3,
			curve: "stepline"
		},
		markers: {
			size: 0
		},
		xaxis: {
			categories: [data.time[0],data.time[1],data.time[2],data.time[3],data.time[4],data.time[5],data.time[6],data.time[7],data.time[8],data.time[9],data.time[10],data.time[11],data.time[12]],
			title: {
				text: "Hour"
			}
		},
		fill: {
			type: "gradient",
			gradient: {
				shadeIntensity: 1,
				opacityFrom: .7,
				opacityTo: .9,
				stops: [0, 90, 100]
			}
		},
		legend: {
			position: "top",
			horizontalAlign: "right",
			floating: !0,
			offsetY: -25,
			offsetX: -5
		}
	};
(chart = new ApexCharts(document.querySelector("#yearly-sale-chart"), options)).render();
options = {
	chart: {
		height: 350,
		type: "rangeBar",
		toolbar: {
			show: !1
		}
	},
	plotOptions: {
		bar: {
			horizontal: !0,
			barHeight: "12%"
		}
	},
	series: [{
		data: [{
			x: 1,
			y: [2,10311,
			],
			fillColor: "#3051d3"
		}, {
			x: 2,
			y: [0,3562
			],
			fillColor: "#e4cc37"
		}, {
			x: 3,
			y: [0,4455
			],
			fillColor: "#F06543"
		}, {
			x: 4,
			y: [0,5126
			],
			fillColor: "#4CB944"
		}]
	}],
	xaxis: {
		type: "num",
		axisBorder: {
			show: !1
		}
	}
};
(chart = new ApexCharts(document.querySelector("#activity-chart"), options)).render();
var
	chart;
options = {
	chart: {
		height: 270,
		type: "radialBar"
	},
	plotOptions: {
		radialBar: {
			hollow: {
				margin: 5,
				size: "38%"
			},
			track: {
				margin: 12
			},
			dataLabels: {
				name: {
					fontSize: "18px",
					offsetY: "-10"
				},
				value: {
					fontSize: "16px",
					offsetY: "5"
				},
				total: {
					show: !0,
					label: data.by+data.bd+'名',
					formatter: function(e) {
						return
						350
					}
				}
			}
		}
	},
	colors: ["#3ddc97", "#f06543"],
	series: [data.bdb, data.byb],
	labels: ["包点用户", "包月用户"]
};
(chart = new ApexCharts(document.querySelector("#radial-chart"), options)).render(), $("#usa-map").vectorMap({
	map: "usa_en",
	enableZoom: !0,
	showTooltip: !0,
	selectedColor: null,
	hoverColor: "#eaf0f1",
	backgroundColor: "transparent",
	color: "#f4f8f9",
	borderColor: "#7c8a96",
	colors: {
		ca: "#385ded",
		tx: "#385ded",
		mt: "#385ded",
		ny: "#385ded"
	},
	onRegionClick: function(e, t, a) {
		e.preventDefault()
	}
});
}