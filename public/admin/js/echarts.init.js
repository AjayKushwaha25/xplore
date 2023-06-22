// Pie Chart
(dom = document.getElementById("totalPurchasePieChart")), (myChart = echarts.init(dom)), (app = {});
(option = null),
(option = {
    tooltip: { trigger: "item", formatter: "{a} <br/>{b} : {c} ({d}%)" },
    legend: { orient: "horizontal", center: "center", bottom: "bottom", data: legendData, textStyle: { color: "#8791af" } },
    color: ["#f46a6a", "#34c38f", "#50a5f1",],
    series: [
        {
            name: "Total Purchase",
            type: "pie",
            radius: "60%",
            center: ["50%", "40%"],
            data: seriesDataArr,
            // data: [
            //     { value: 10, name: "Indie Mint" },
            //     { value: 10, name: "Fresh Mint" },
            //     { value: 10, name: "Smart" },
            // ],
            itemStyle: { emphasis: { shadowBlur: 10, shadowOffsetX: 0, shadowColor: "rgba(0, 0, 0, 0.5)" } },
        },
    ],
}),
option && "object" == typeof option && myChart.setOption(option, !0);


// Bar 
(dom = document.getElementById("totalPurchaseBarGraph")), (myChart = echarts.init(dom)), (app = {});
(option = null),
(option = {
    tooltip: { trigger: "axis", axisPointer: { type: 'shadow'} },
    // legend: { orient: "horizontal", center: "center", bottom: "bottom", data: legendData, textStyle: { color: "#8791af" } },
    legend: {},
    grid: {
        left: '3%',
        right: '4%',
        bottom: '3%',
        containLabel: true
    },
    xAxis: [
        {
          type: 'category',
          data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
        }
    ],
     yAxis: [
        {
          type: 'value'
        }
    ],
    color: ["#f46a6a", "#34c38f", "#50a5f1",],
    series: [
        {
            name: 'Email',
            type: 'bar',
            stack: 'A',
            emphasis: {
              focus: 'series'
            },
            data: [120, 132, 101, 134, 90, 230, 210]
        },
        {
            name: 'Union Ads',
            type: 'bar',
            stack: 'A',
            emphasis: {
              focus: 'series'
            },
            data: [220, 182, 191, 234, 290, 330, 310]
        },
        {
            name: 'Video Ads',
            type: 'bar',
            stack: 'A',
            emphasis: {
              focus: 'series'
            },
            data: [150, 232, 201, 154, 190, 330, 410]
        },
    ],
}),
option && "object" == typeof option && myChart.setOption(option, !0);
