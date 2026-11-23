/**
 * Carta dashboard (Chart.js). Semua data ialah contoh.
 * Carta tab pertama dibina serta-merta; yang lain dibina apabila tab dipaparkan.
 */
import { noMotion, fmt } from './shared.js';

/* ---------- Carta dashboard (semua data ialah contoh) ---------- */
var built = {};

export function initCharts() {
    if (typeof Chart === 'undefined') {
        document.querySelectorAll('.cc-body').forEach(function (b) {
            b.textContent = 'Carta tidak dapat dimuatkan. Semak sambungan internet.';
        });
        return;
    }

    var C = {
        lime: '#5e9f1f',
        limeHi: '#3c7a0e',
        sky: '#1f86dc',
        amber: '#e8960c',
        violet: '#7a6af0',
        grey: '#7d8b9c',
        forest: '#1f9463',
        slate: '#4b6a8a'
    };
    var D = Chart.defaults;
    D.font.family = "'Plus Jakarta Sans', system-ui, sans-serif";
    D.font.size = 12;
    D.color = '#5a6b7e';
    D.borderColor = 'rgba(11,37,64,.08)';
    D.maintainAspectRatio = false;
    if (noMotion) D.animation = false;
    D.plugins.legend.labels.usePointStyle = true;
    D.plugins.legend.labels.boxWidth = 8;
    D.plugins.legend.labels.boxHeight = 8;
    D.plugins.tooltip.backgroundColor = '#0b2540';
    D.plugins.tooltip.borderColor = 'rgba(159,176,195,.3)';
    D.plugins.tooltip.borderWidth = 1;
    D.plugins.tooltip.padding = 10;
    D.plugins.tooltip.titleColor = '#ffffff';
    D.plugins.tooltip.bodyColor = '#e8eef5';

    function n(v) {
        return Number(v).toLocaleString('en-US');
    }

    function make(id, cfg) {
        var c = document.getElementById(id);
        if (c) new Chart(c, cfg);
    }

    function pct(a, b) {
        return Math.round(a / b * 1000) / 10;
    }

    /* Nilai hujung bar untuk carta bar mendatar */
    var endLabels = {
        id: 'endLabels',
        afterDatasetsDraw: function (chart, args, opts) {
            if (!opts || !opts.format) return;
            var ctx = chart.ctx;
            ctx.save();
            ctx.fillStyle = '#1c2b3d';
            ctx.font = '600 12px ' + D.font.family;
            ctx.textBaseline = 'middle';
            chart.getDatasetMeta(0).data.forEach(function (bar, i) {
                ctx.fillText(opts.format(chart.data.datasets[0].data[i]), bar.x + 8, bar.y);
            });
            ctx.restore();
        }
    };

    function donut(id, labels, data, colors, unit) {
        make(id, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors,
                    borderColor: '#ffffff',
                    borderWidth: 3
                }]
            },
            options: {
                cutout: '62%',
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function (c) {
                                return ' ' + c.label + ': ' + n(c.parsed) + unit;
                            }
                        }
                    }
                }
            }
        });
    }

    function hbar(id, labels, data, colors, fmt) {
        make(id, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors,
                    borderRadius: 4,
                    barThickness: 20
                }]
            },
            options: {
                indexAxis: 'y',
                layout: {
                    padding: {
                        right: 56
                    }
                },
                scales: {
                    x: {
                        display: false,
                        beginAtZero: true
                    },
                    y: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    endLabels: {
                        format: fmt
                    },
                    tooltip: {
                        callbacks: {
                            label: function (c) {
                                return ' ' + fmt(c.parsed.x);
                            }
                        }
                    }
                }
            },
            plugins: [endLabels]
        });
    }

    /* Data contoh yang saling konsisten (angka 12,480 / 34% / 41% pada kad KPI diterbitkan daripada tatasusunan ini) */
    var M = ['Jan', 'Feb', 'Mac', 'Apr', 'Mei', 'Jun', 'Jul', 'Ogo', 'Sep', 'Okt', 'Nov', 'Dis'];
    var dil = [1130, 1115, 1100, 1085, 1070, 1050, 1035, 1020, 1005, 985, 965, 920];
    var kit = [540, 555, 570, 585, 595, 605, 615, 625, 635, 645, 655, 665];
    var kom = [105, 108, 111, 114, 117, 120, 123, 126, 129, 132, 135, 138];
    var jana = dil.map(function (d, i) {
        return d + kit[i] + kom[i];
    });
    var rKit = kit.map(function (k, i) {
        return pct(k, jana[i]);
    });
    var rAlih = kit.map(function (k, i) {
        return pct(k + kom[i], jana[i]);
    });

    var builders = {
        eks: function () {
            make('c-bulan', {
                type: 'bar',
                data: {
                    labels: M,
                    datasets: [{
                        label: 'Dilupuskan',
                        data: dil,
                        backgroundColor: C.slate,
                        stack: 't'
                    },
                    {
                        label: 'Dikitar semula',
                        data: kit,
                        backgroundColor: C.lime,
                        stack: 't'
                    },
                    {
                        label: 'Dikompos',
                        data: kom,
                        backgroundColor: C.sky,
                        stack: 't'
                    },
                    {
                        type: 'line',
                        label: 'Kadar pengalihan (%)',
                        data: rAlih,
                        yAxisID: 'y1',
                        order: -1,
                        borderColor: C.amber,
                        backgroundColor: C.amber,
                        tension: .3,
                        pointRadius: 3
                    }
                    ]
                },
                options: {
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    scales: {
                        x: {
                            stacked: true,
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            stacked: true,
                            ticks: {
                                callback: n
                            }
                        },
                        y1: {
                            position: 'right',
                            min: 30,
                            max: 50,
                            grid: {
                                drawOnChartArea: false
                            },
                            ticks: {
                                callback: function (v) {
                                    return v + '%';
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function (c) {
                                    return ' ' + c.dataset.label + ': ' + (c.dataset.yAxisID === 'y1' ? c.parsed.y + '%' : n(c.parsed.y) + ' tan');
                                }
                            }
                        }
                    }
                }
            });

            donut('c-zon', ['Zon 1', 'Zon 2', 'Zon 3', 'Zon 4', 'Zon 5', 'Zon 6'], [24, 21, 19, 16, 12, 8],
                [C.lime, C.sky, C.amber, C.violet, C.grey, C.forest], '%');

            make('c-tahun', {
                type: 'bar',
                data: {
                    labels: ['2022', '2023', '2024', '2025'],
                    datasets: [{
                        label: 'Dilupuskan',
                        data: [14100, 13500, 12900, 12480],
                        backgroundColor: C.slate,
                        stack: 't'
                    },
                    {
                        label: 'Dikitar semula',
                        data: [5200, 5900, 6600, 7290],
                        backgroundColor: C.lime,
                        stack: 't'
                    },
                    {
                        label: 'Dikompos',
                        data: [900, 1100, 1300, 1458],
                        backgroundColor: C.sky,
                        stack: 't'
                    }
                    ]
                },
                options: {
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    scales: {
                        x: {
                            stacked: true,
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            stacked: true,
                            ticks: {
                                callback: n
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function (c) {
                                    return ' ' + c.dataset.label + ': ' + n(c.parsed.y) + ' tan';
                                }
                            }
                        }
                    }
                }
            });

            donut('c-sektor', ['Kediaman', 'Komersial', 'Institusi', 'Perindustrian', 'Pembinaan'], [48, 27, 10, 9, 6],
                [C.lime, C.sky, C.amber, C.violet, C.grey], '%');
        },

        ops: function () {
            donut('c-sumber', ['Luar Shah Alam', 'Kawasan Shah Alam', 'Panel MBSA'], [1088, 348, 58], [C.sky, C.lime, C.amber], ' tan');
            hbar('c-urus', ['Batu', 'Sisa pelupusan', 'Besi', 'Kotak', 'Plastik', 'Kayu'], [83.1, 15.9, 0.8, 0.1, 0.1, 0.0],
                [C.lime, C.amber, C.lime, C.lime, C.lime, C.lime],
                function (v) {
                    return Number(v).toFixed(1) + '%';
                });
            make('c-terima', {
                type: 'bar',
                data: {
                    labels: M.slice(0, 9),
                    datasets: [{
                        data: [1240, 1310, 1275, 1390, 1420, 1365, 1450, 1480, 1494],
                        backgroundColor: C.lime,
                        borderRadius: 3
                    }]
                },
                options: {
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: n
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function (c) {
                                    return ' ' + n(c.parsed.y) + ' tan';
                                }
                            }
                        }
                    }
                }
            });
        },

        kpi: function () {
            make('c-kadar', {
                type: 'line',
                data: {
                    labels: M,
                    datasets: [{
                        label: 'Kadar pengalihan',
                        data: rAlih,
                        borderColor: C.sky,
                        backgroundColor: C.sky,
                        tension: .3,
                        pointRadius: 3
                    },
                    {
                        label: 'Kadar kitar semula',
                        data: rKit,
                        borderColor: C.lime,
                        backgroundColor: C.lime,
                        tension: .3,
                        pointRadius: 3
                    },
                    {
                        label: 'Sasaran pengalihan',
                        data: M.map(function () {
                            return 40;
                        }),
                        borderColor: C.grey,
                        borderDash: [6, 4],
                        pointRadius: 0,
                        borderWidth: 1.5
                    }
                    ]
                },
                options: {
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            min: 25,
                            max: 50,
                            ticks: {
                                callback: function (v) {
                                    return v + '%';
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function (c) {
                                    return ' ' + c.dataset.label + ': ' + c.parsed.y + '%';
                                }
                            }
                        }
                    }
                }
            });

            make('c-kapita', {
                type: 'line',
                data: {
                    labels: M,
                    datasets: [{
                        label: 'kg/orang/hari',
                        data: [1.24, 1.23, 1.22, 1.21, 1.21, 1.20, 1.19, 1.19, 1.18, 1.18, 1.17, 1.16],
                        borderColor: C.lime,
                        backgroundColor: 'rgba(94,159,31,.15)',
                        fill: true,
                        tension: .3,
                        pointRadius: 3
                    }]
                },
                options: {
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            min: 1.1,
                            max: 1.3,
                            ticks: {
                                callback: function (v) {
                                    return v.toFixed(2);
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function (c) {
                                    return ' ' + c.parsed.y.toFixed(2) + ' kg/orang/hari';
                                }
                            }
                        }
                    }
                }
            });

            var cat = {
                makanan: [],
                plastik: [],
                kertas: [],
                lain: []
            };
            jana.forEach(function (t, i) {
                var k = i / 11;
                var a = Math.round(t * (0.44 - 0.04 * k)),
                    b = Math.round(t * (0.17 + 0.02 * k)),
                    c = Math.round(t * (0.13 + 0.02 * k));
                cat.makanan.push(a);
                cat.plastik.push(b);
                cat.kertas.push(c);
                cat.lain.push(t - a - b - c);
            });

            function area(label, data, color, i) {
                return {
                    label: label,
                    data: data,
                    borderColor: color,
                    backgroundColor: color + 'cc',
                    fill: i === 0 ? 'origin' : '-1',
                    tension: .3,
                    pointRadius: 0,
                    borderWidth: 1
                };
            }
            make('c-kategori', {
                type: 'line',
                data: {
                    labels: M,
                    datasets: [
                        area('Sisa makanan', cat.makanan, C.lime, 0), area('Plastik', cat.plastik, C.sky, 1),
                        area('Kertas', cat.kertas, C.amber, 2), area('Lain-lain', cat.lain, C.violet, 3)
                    ]
                },
                options: {
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            stacked: true,
                            min: 0,
                            ticks: {
                                callback: n
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function (c) {
                                    return ' ' + c.dataset.label + ': ' + n(c.parsed.y) + ' tan';
                                }
                            }
                        }
                    }
                }
            });

            var yrs = ['2020', '2021', '2022', '2023', '2024', '2025', '2026', '2027', '2028', '2029', '2030'];
            var hist = [19400, 19800, 20200, 20500, 20800, 21228];

            function proj(rate) {
                var o = [];
                for (var i = 0; i < 5; i++) o.push(Math.round(21228 * Math.pow(1 + rate, i + 1)));
                return o;
            }

            function pad(a) {
                return a.concat(Array(yrs.length - a.length).fill(null));
            }

            function lead(a) {
                return Array(5).fill(null).concat([21228]).concat(a);
            }
            make('c-unjuran', {
                type: 'line',
                data: {
                    labels: yrs,
                    datasets: [{
                        label: 'Sejarah',
                        data: pad(hist),
                        borderColor: C.lime,
                        backgroundColor: C.lime,
                        tension: .25,
                        pointRadius: 3
                    },
                    {
                        label: 'Trend semasa',
                        data: lead(proj(0.015)),
                        borderColor: C.amber,
                        backgroundColor: C.amber,
                        borderDash: [6, 4],
                        tension: .25,
                        pointRadius: 0
                    },
                    {
                        label: 'Dengan pengurangan sisa',
                        data: lead(proj(0.005)),
                        borderColor: C.sky,
                        backgroundColor: C.sky,
                        borderDash: [6, 4],
                        tension: .25,
                        pointRadius: 0
                    }
                    ]
                },
                options: {
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            min: 18000,
                            ticks: {
                                callback: n
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function (c) {
                                    return ' ' + c.dataset.label + ': ' + n(c.parsed.y) + ' tan';
                                }
                            }
                        }
                    }
                }
            });
        },

        prog: function () {
            donut('c-status', ['Dirancang', 'Sedang dilaksanakan', 'Selesai', 'Ditangguhkan'], [32, 48, 36, 12], [C.sky, C.lime, C.forest, C.amber], ' program');
            hbar('c-laporan', ['Draf', 'Untuk semakan', 'Perlu pembetulan', 'Disahkan'], [18, 24, 14, 72],
                [C.grey, C.sky, C.amber, C.lime],
                function (v) {
                    return String(v);
                });
            make('c-aktiviti', {
                type: 'bar',
                data: {
                    labels: M,
                    datasets: [{
                        data: [5, 7, 8, 9, 11, 10, 9, 11, 13, 14, 16, 15],
                        backgroundColor: C.sky,
                        borderRadius: 3
                    }]
                },
                options: {
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function (c) {
                                    return ' ' + c.parsed.y + ' program';
                                }
                            }
                        }
                    }
                }
            });
        }
    };

    function build(key) {
        if (built[key]) return;
        built[key] = true;
        if (builders[key]) builders[key]();
    }
    build('eks');
    /* Carta lain dibina apabila tab dipaparkan, kerana canvas dalam tab tersembunyi tidak mempunyai saiz */
    document.querySelectorAll('#dashTabs [data-key]').forEach(function (t) {
        t.addEventListener('shown.bs.tab', function () {
            build(t.getAttribute('data-key'));
        });
    });
}
