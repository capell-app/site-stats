<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    />
    <title>Site statistics metrics dashboard</title>
    <style>
        :root {
            color-scheme: light;
            font-family:
                Inter,
                ui-sans-serif,
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                'Segoe UI',
                sans-serif;
            background: #f6f7fb;
            color: #172033;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background:
                radial-gradient(
                    circle at 8% 0%,
                    rgba(99, 102, 241, 0.12),
                    transparent 32rem
                ),
                #f6f7fb;
        }

        .site-stats-metrics-dashboard {
            margin: 0 auto;
            min-height: 100vh;
            max-width: 1280px;
            padding: 44px 42px 64px;
        }

        .dashboard-header {
            align-items: flex-end;
            display: flex;
            gap: 24px;
            justify-content: space-between;
            margin-bottom: 28px;
        }

        .eyebrow {
            color: #4f46e5;
            font-size: 12px;
            font-weight: 750;
            letter-spacing: 0.11em;
            margin: 0 0 10px;
            text-transform: uppercase;
        }

        h1 {
            font-size: 35px;
            letter-spacing: -0.035em;
            line-height: 1.08;
            margin: 0;
        }

        .summary {
            color: #667085;
            font-size: 15px;
            line-height: 1.6;
            margin: 12px 0 0;
            max-width: 700px;
        }

        .period {
            background: #fff;
            border: 1px solid #dfe3ec;
            border-radius: 10px;
            box-shadow: 0 4px 14px rgba(23, 32, 51, 0.04);
            color: #344054;
            font-size: 14px;
            font-weight: 650;
            padding: 11px 15px;
            white-space: nowrap;
        }

        .metric-grid {
            display: grid;
            gap: 22px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .metric-card {
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid #e0e4ed;
            border-radius: 18px;
            box-shadow: 0 18px 42px rgba(23, 32, 51, 0.07);
            padding: 26px 28px 24px;
        }

        .metric-heading {
            align-items: flex-start;
            display: flex;
            gap: 18px;
            justify-content: space-between;
        }

        .metric-label {
            color: #344054;
            font-size: 14px;
            font-weight: 700;
            margin: 0 0 9px;
        }

        .metric-value {
            font-size: 42px;
            font-variant-numeric: tabular-nums;
            font-weight: 760;
            letter-spacing: -0.045em;
            line-height: 1;
            margin: 0;
        }

        .metric-description {
            color: #667085;
            font-size: 13px;
            margin: 9px 0 0;
        }

        .metric-change {
            background: #ecfdf3;
            border: 1px solid #d1fadf;
            border-radius: 999px;
            color: #027a48;
            font-size: 12px;
            font-weight: 700;
            padding: 7px 10px;
            white-space: nowrap;
        }

        .chart {
            height: 184px;
            margin-top: 24px;
            position: relative;
        }

        .grid-line {
            border-top: 1px solid #eaecf0;
            left: 0;
            position: absolute;
            right: 0;
        }

        .grid-line:nth-child(1) {
            top: 15%;
        }
        .grid-line:nth-child(2) {
            top: 48%;
        }
        .grid-line:nth-child(3) {
            top: 81%;
        }

        .bars {
            align-items: end;
            bottom: 24px;
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(7, 1fr);
            left: 0;
            position: absolute;
            right: 0;
            top: 0;
        }

        .bar-column {
            align-items: center;
            display: flex;
            height: 100%;
            justify-content: flex-end;
            flex-direction: column;
        }

        .bar {
            border-radius: 6px 6px 3px 3px;
            min-height: 12px;
            width: min(38px, 76%);
        }

        .metric-card--indigo .bar {
            background: linear-gradient(180deg, #818cf8, #4f46e5);
        }

        .metric-card--emerald .bar {
            background: linear-gradient(180deg, #6ee7b7, #059669);
        }

        .day {
            color: #98a2b3;
            font-size: 10px;
            margin-top: 8px;
            white-space: nowrap;
        }

        .dashboard-footer {
            align-items: center;
            background: rgba(255, 255, 255, 0.78);
            border: 1px solid #e0e4ed;
            border-radius: 13px;
            color: #667085;
            display: flex;
            font-size: 13px;
            gap: 12px;
            justify-content: space-between;
            margin-top: 22px;
            padding: 15px 18px;
        }

        .status {
            align-items: center;
            color: #344054;
            display: flex;
            font-weight: 650;
            gap: 8px;
        }

        .status-dot {
            background: #12b76a;
            border-radius: 999px;
            box-shadow: 0 0 0 4px #d1fadf;
            height: 8px;
            width: 8px;
        }

        @media (max-width: 760px) {
            .site-stats-metrics-dashboard {
                padding: 26px 18px 44px;
            }
            .dashboard-header {
                align-items: flex-start;
                flex-direction: column;
            }
            .metric-grid {
                grid-template-columns: 1fr;
            }
            .dashboard-footer {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <main class="site-stats-metrics-dashboard">
        <header class="dashboard-header">
            <div>
                <p class="eyebrow">Content metrics</p>
                <h1>Site statistics</h1>
                <p class="summary">Daily global totals collected from published content and presented through the host dashboard.</p>
            </div>
            <div class="period">{{ $period }}</div>
        </header>

        <section
            class="metric-grid"
            aria-label="Content total trends"
        >
            @foreach ($metrics as $metric)
                @php
                $maximum = max($metric['points']);
                $minimum = min($metric['points']);
                $range = max(1, $maximum - $minimum);
            @endphp
                <article class="metric-card metric-card--{{ $metric['tone'] }}">
                    <div class="metric-heading">
                        <div>
                            <p class="metric-label">{{ $metric['label'] }}</p>
                            <p class="metric-value">{{ number_format($metric['latest']) }}</p>
                            <p class="metric-description">{{ $metric['description'] }}</p>
                        </div>
                        <span
                            class="metric-change"
                            >{{ $metric['change'] }}</span
                        >
                    </div>

                    <div
                        class="chart"
                        aria-label="{{ $metric['label'] }} daily total trend"
                    >
                        <span class="grid-line"></span>
                        <span class="grid-line"></span>
                        <span class="grid-line"></span>
                        <div class="bars">
                            @foreach ($metric['points'] as $index => $point)
                                @php
                                $height = 34 + (int) round((($point - $minimum) / $range) * 66);
                            @endphp
                                <div class="bar-column">
                                    <span
                                        class="bar"
                                        style="height: {{ $height }}%"
                                        title="{{ $point }}"
                                    ></span>
                                    <span class="day">{{ $days[$index] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </article>
            @endforeach
        </section>

        <footer class="dashboard-footer">
            <span class="status"
                ><span class="status-dot"></span>Daily collection complete</span
            >
            <span>Global scope · Updated {{ $updatedAt }}</span>
        </footer>
    </main>
</body>
</html>
