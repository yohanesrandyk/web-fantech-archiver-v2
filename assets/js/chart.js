function initMultiChart(id, type, series, category, min, unit) {
    var element = document.getElementById(id);
    var height = parseInt(KTUtil.css(element, 'height'));

    var options = {
        series: series,
        chart: {
            type: type,
            height: height,
            toolbar: {
                show: true
            }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: ['40%']
            },
        },
        legend: {
            show: true
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: category,
            axisBorder: {
                show: false,
            },
            axisTicks: {
                show: false
            },
            labels: {
                style: {
                    colors: KTApp.getSettings()['colors']['gray']['gray-500'],
                    fontSize: '12px',
                    fontFamily: KTApp.getSettings()['font-family']
                }
            }
        },
        yaxis: {
            show: true,
            labels: {
                style: {
                    colors: KTApp.getSettings()['colors']['gray']['gray-500'],
                    fontSize: '12px',
                    fontFamily: KTApp.getSettings()['font-family']
                },
                formatter: function (val) {
                    return formatNumber(val)
                }
            },
            min: min
        },
        fill: {
            opacity: 1
        },
        states: {
            normal: {
                filter: {
                    type: 'none',
                    value: 0
                }
            },
            hover: {
                filter: {
                    type: 'none',
                    value: 0
                }
            },
            active: {
                allowMultipleDataPointsSelection: false,
                filter: {
                    type: 'none',
                    value: 0
                }
            }
        },
        tooltip: {
            style: {
                fontSize: '12px',
                fontFamily: KTApp.getSettings()['font-family']
            },
            y: {
                formatter: function (val) {
                    return formatNumber(val) + unit
                }
            }
        },
        colors: [KTApp.getSettings()['colors']['theme']['base']['primary'], KTApp.getSettings()['colors']['theme']['base']['success'], KTApp.getSettings()['colors']['theme']['base']['danger']],
        grid: {
            borderColor: KTApp.getSettings()['colors']['gray']['gray-200'],
            strokeDashArray: 4,
            yaxis: {
                lines: {
                    show: true
                }
            }
        }
    };

    var chart = new ApexCharts(element, options);
    chart.render();
}
function initPieChart(id, type, series, labels) {
    var element = document.getElementById(id);
    var height = parseInt(KTUtil.css(element, 'height'));

    if (!element) {
        return;
    }

    var options = {
        series: series,
        chart: {
            type: type,
            height: height
        },
        labels: labels,
        legend: {
            show: true,
            position: "bottom"
        },
        dataLabels: {
            enabled: false
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return formatNumber(val)
                }
            }
        }
    };

    var chart = new ApexCharts(element, options);
    chart.render();
}
function initGaugeChart(id, title, value) {
    var element = document.getElementById(id);
    var height = parseInt(KTUtil.css(element, 'height'));

    if (!element) {
        return;
    }

    var options = {
        series: [value],
        chart: {
            height: height,
            type: 'radialBar',
        },
        plotOptions: {
            radialBar: {
                startAngle: -135,
                endAngle: 135,
                dataLabels: {
                    showOn: "always",
                    name: {
                        show: false,
                        fontWeight: '700',
                        offsetY: 140,
                    },
                    value: {
                        color: KTApp.getSettings()['colors']['gray']['gray-700'],
                        fontSize: "30px",
                        fontWeight: '700',
                        offsetY: 10,
                        show: true,
                        formatter: function (val) {
                            return val + '%';
                        }
                    }
                },
                track: {
                    background: KTApp.getSettings()['colors']['theme']['light']['success'],
                    strokeWidth: '100%'
                }
            }
        },
        colors: [KTApp.getSettings()['colors']['theme']['base']['success']],
        stroke: {
            dashArray: 4
        },
        labels: [title]
    };

    var chart = new ApexCharts(element, options);
    chart.render();
}
function initSparkChart(id, title, data, category) {
    var element = document.getElementById(id);
    var height = parseInt(KTUtil.css(element, 'height'));

    if (!element) {
        return;
    }

    var options = {
        series: [{
            name: title,
            data: data
        }],
        chart: {
            type: 'area',
            height: height,
            toolbar: {
                show: false
            },
            zoom: {
                enabled: false
            },
            sparkline: {
                enabled: true
            }
        },
        plotOptions: {},
        legend: {
            show: false
        },
        dataLabels: {
            enabled: false
        },
        fill: {
            type: 'gradient',
            opacity: 1,
            gradient: {

                type: "vertical",
                shadeIntensity: 0.5,
                gradientToColors: undefined,
                inverseColors: true,
                opacityFrom: 1,
                opacityTo: 0.375,
                stops: [25, 50, 100],
                colorStops: []
            }
        },
        stroke: {
            curve: 'smooth',
            show: true,
            width: 3,
            colors: [KTApp.getSettings()['colors']['theme']['base']['success']]
        },
        xaxis: {
            categories: category,
            axisBorder: {
                show: false,
            },
            axisTicks: {
                show: false
            },
            labels: {
                show: false,
                style: {
                    colors: KTApp.getSettings()['colors']['gray']['gray-500'],
                    fontSize: '12px',
                    fontFamily: KTApp.getSettings()['font-family']
                }
            },
            crosshairs: {
                show: false,
                position: 'front',
                stroke: {
                    color: KTApp.getSettings()['colors']['gray']['gray-300'],
                    width: 1,
                    dashArray: 3
                }
            },
            tooltip: {
                enabled: true,
                formatter: undefined,
                offsetY: 0,
                style: {
                    fontSize: '12px',
                    fontFamily: KTApp.getSettings()['font-family']
                }
            }
        },
        yaxis: {
            // min: 0,
            // max: 65,
            labels: {
                show: false,
                style: {
                    colors: KTApp.getSettings()['colors']['gray']['gray-500'],
                    fontSize: '12px',
                    fontFamily: KTApp.getSettings()['font-family']
                }
            }
        },
        states: {
            normal: {
                filter: {
                    type: 'none',
                    value: 0
                }
            },
            hover: {
                filter: {
                    type: 'none',
                    value: 0
                }
            },
            active: {
                allowMultipleDataPointsSelection: false,
                filter: {
                    type: 'none',
                    value: 0
                }
            }
        },
        tooltip: {
            style: {
                fontSize: '12px',
                fontFamily: KTApp.getSettings()['font-family']
            },
            y: {
                formatter: function (val) {
                    return "IDR " + formatNumber(val)
                }
            }
        },
        colors: [KTApp.getSettings()['colors']['theme']['light']['success']],
        markers: {
            colors: [KTApp.getSettings()['colors']['theme']['light']['success']],
            strokeColor: [KTApp.getSettings()['colors']['theme']['base']['success']],
            strokeWidth: 3
        }
    };

    var chart = new ApexCharts(element, options);
    chart.render();
}