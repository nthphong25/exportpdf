@php
    $count = count($timeline);
    $ATQC20 = ['ATQC2001', 'ATQC2002', 'ATQC2003', 'ATQC2004', 'ATQC2005', 'ATQC2006', 'ATQC2007', 'ATQC2008', 'ATQC2009', 'ATQC2010', 'ATQC2011', 'ATQC2012', 'ATQC2013', 'ATQC2014', 'ATQC20_qi_others'];
    $ATQC30 = ['ATQC3001', 'ATQC3002', 'ATQC3003', 'ATQC3004', 'ATQC3005', 'ATQC3006', 'ATQC3007', 'ATQC3008', 'ATQC3009', 'ATQC3010', 'ATQC3011', 'ATQC3012', 'ATQC3013', 'ATQC30_qi_others'];
    $ATQC40 = ['ATQC4001', 'ATQC4002', 'ATQC4003', 'ATQC4004', 'ATQC4005', 'ATQC4006', 'ATQC4007', 'ATQC4008', 'ATQC4009', 'ATQC40_qi_others'];
    $error = ['ATQC2001', 'ATQC2002', 'ATQC2014', 'ATQC4004', 'ATQC4005', 'ATQC3004', 'ATQC2004', 'ATQC4009', 'ATQC2005', 'ATQC3001', 'ATQC3002', 'ATQC3003', 'ATQC3009', 'ATQC3012', 'ATQC3013', 'ATQC4002', 'ATQC4003', 'ATQC4001', 'ATQC2006', 'ATQC2007', 'ATQC2003', 'ATQC2011', 'ATQC3005', 'ATQC2009', 'ATQC2010', 'ATQC3008', 'ATQC4006', 'ATQC2008', 'ATQC4008', 'ATQC2012', 'ATQC2013', 'ATQC3007', 'ATQC3011', 'ATQC3010', 'ATQC3006', 'ATQC4007'];
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title> PDF Example</title>

</head>

<body>
    <script src="{{ asset('js/app.js') }}" type="text/js"></script>
    <script>
        const imageCollection = [];
    </script>
    <div class="justify-center items-center text-center mt-8">
        @if (isset($allDataByDayAndDept))
            @foreach ($allDataByDayAndDept as $day => $datas)
                @php

                    $h = 0;
                    $day = str_replace('-', '_', $day);
                @endphp
                @foreach ($datas as $index1 => $items)
                    {{-- @dd($items->ATQC20_time_log); --}}
                    @if ($selectedGL == 'GL4')
                        @php
                            $maxValues = [];
                            $timeSlot = '07:00-08:00';
                            foreach ($error as $label) {
                                $maxValue = 0;

                                $logData20 = json_decode($items->ATQC20_time_log, true);
                                $logData30 = json_decode($items->ATQC30_time_log, true);
                                $logData40 = json_decode($items->ATQC40_time_log, true);

                                $value20 = $logData20[$label][$timeSlot] ?? 0;
                                $value30 = $logData30[$label][$timeSlot] ?? 0;
                                $value40 = $logData40[$label][$timeSlot] ?? 0;

                                $totalValue = $value20 + $value30 + $value40;

                                if ($totalValue >= $maxValue) {
                                    $maxValue = $totalValue;
                                    $maxLabel = $label;
                                }

                                $maxValues[] = [
                                    'label' => $maxLabel,
                                    'value' => $maxValue,
                                ];
                            }

                            // Lặp qua các label không có trong index_error
                            $allLabels = array_merge($ATQC20, $ATQC30, $ATQC40);
                            $nonErrorLabels = array_diff($allLabels, $error);

                            foreach ($nonErrorLabels as $label) {
                                $maxValue = 0;

                                $logData20 = json_decode($items->ATQC20_time_log, true);
                                $logData30 = json_decode($items->ATQC30_time_log, true);
                                $logData40 = json_decode($items->ATQC40_time_log, true);

                                $value20 = $logData20[$label][$timeSlot] ?? 0;
                                $value30 = $logData30[$label][$timeSlot] ?? 0;
                                $value40 = $logData40[$label][$timeSlot] ?? 0;

                                $totalValue = $value20 + $value30 + $value40;

                                if ($totalValue >= $maxValue) {
                                    $maxValue = $totalValue;
                                    $maxLabel = $label;
                                }

                                $maxValues[] = [
                                    'label' => $maxLabel,
                                    'value' => $maxValue,
                                ];
                            }

                            // Sắp xếp mảng $maxValues theo giá trị giảm dần
                            usort($maxValues, function ($a, $b) {
                                return $b['value'] - $a['value'];
                            });

                            // Lấy 3 giá trị lớn nhất từ mảng $maxValues
                            $top3MaxValues = array_slice($maxValues, 0, 3);
                            // dd($top3MaxValues);
                        @endphp
                    @else
                        @php
                            $maxValues = [];
                            $tempMaxValues = []; // Mảng tạm thời

                            foreach ($error as $label) {
                                $maxValue = 0;

                                $logData20 = json_decode($items->ATQC20_time_log, true);
                                $logData30 = json_decode($items->ATQC30_time_log, true);
                                $logData40 = json_decode($items->ATQC40_time_log, true);

                                foreach ($timeline as $timeSlot) {
                                    $value20 = $logData20[$label][$timeSlot] ?? 0;
                                    $value30 = $logData30[$label][$timeSlot] ?? 0;
                                    $value40 = $logData40[$label][$timeSlot] ?? 0;

                                    $totalValue = $value20 + $value30 + $value40;

                                    if ($totalValue >= $maxValue) {
                                        $maxValue = $totalValue;
                                        $maxLabel = $label;
                                    }
                                }

                                $tempMaxValues[] = [
                                    'label' => $maxLabel,
                                    'value' => $maxValue,
                                ];
                            }

                            $allLabels = array_merge($ATQC20, $ATQC30, $ATQC40);
                            $nonErrorLabels = array_diff($allLabels, $error);

                            foreach ($nonErrorLabels as $label) {
                                $maxValue = 0;

                                $logData20 = json_decode($items->ATQC20_time_log, true);
                                $logData30 = json_decode($items->ATQC30_time_log, true);
                                $logData40 = json_decode($items->ATQC40_time_log, true);

                                foreach ($timeline as $timeSlot) {
                                    $value20 = $logData20[$label][$timeSlot] ?? 0;
                                    $value30 = $logData30[$label][$timeSlot] ?? 0;
                                    $value40 = $logData40[$label][$timeSlot] ?? 0;

                                    $totalValue = $value20 + $value30 + $value40;

                                    if ($totalValue >= $maxValue) {
                                        $maxValue = $totalValue;
                                        $maxLabel = $label;
                                    }
                                }

                                $tempMaxValues[] = [
                                    'label' => $maxLabel,
                                    'value' => $maxValue,
                                ];
                            }

                            // Sắp xếp mảng tạm thời $tempMaxValues theo giá trị giảm dần
                            usort($tempMaxValues, function ($a, $b) {
                                return $b['value'] - $a['value'];
                            });

                            // Lấy 3 giá trị lớn nhất từ mảng tạm thời $tempMaxValues
                            $top3MaxValues = array_slice($tempMaxValues, 0, 3);

                            // dd($top3MaxValues);

                        @endphp
                    @endif
                    @php
                        $data = [
                            'day' => $day,
                            'ATQC20' => $logData20new1,
                            'ATQC30' => $logData30new1,
                            'ATQC40' => $logData40new1,
                        ];

                    @endphp
                    <table class="border border-black mx-auto" width="1152" height="250"
                        id="customers_{{ $day }}_{{ $h }}">
                        <thead>
                            <tr>
                                <th class="w-10 text-xs	 px-0 py-0 bg-gray-400 border border-black">#id</th>

                                <th class="w-10 text-xs	 px-0 py-0 bg-gray-400 border border-black">#NO</th>
                                <th class="w-16 text-xs	 px-0 py-0 bg-gray-400 border border-black">INSPECTED DATE</th>
                                <th class="w-16 text-xs	 px-0 py-0 bg-gray-400 border border-black">HOURS</th>
                                <th class="w-16 text-xs	 px-0 py-0 bg-gray-400 border border-black">BRAND</th>
                                <th class="w-16 text-xs	 px-0 py-0 bg-gray-400 border border-black">LINE NAME</th>
                                <th class="w-16 text-xs	 px-0 py-0 bg-gray-400 border border-black">TQC NAME</th>
                                <th class="w-16 text-xs	 px-0 py-0 bg-gray-400 border border-black">DEFECT QTY</th>
                                @foreach ($top3MaxValues as $d)
                                    <th class="w-16 text-xs px-0 py-0 bg-gray-400 border border-black">
                                        {{ $d['label'] }}</th>
                                    <th class="w-16 text-xs	px-0 py-0 bg-gray-400 border border-black">
                                        {{ $d['label'] }} %
                                    </th>
                                @endforeach
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($timeline as $index => $time)
                                <tr class="border border-black">
                                    <td class="text-xs px-0 py-0 border-r border-black">{{ $items->keyid }}</td>
                                    <td class="text-xs px-0 py-0 border-r border-black">{{ $index }}</td>
                                    <td class="text-xs px-0 py-0 border-r border-black">
                                        {{ date('j-M', strtotime($items->qip_date)) }}
                                    </td>
                                    <td class="text-xs px-0 py-0 border-r border-black">{{ $time }}</td>
                                    <td class="text-xs px-0 py-0 border-r border-black">{{ $items->custbrand_id }}</td>
                                    <td class="text-xs px-0 py-0 border-r border-black">{{ $items->qc_dept_code }}</td>
                                    <td class="text-xs px-0 py-0 border-r border-black">{{ $items->qc_dept_code }}</td>
                                    @php
                                        $totalRow = 0; // Biến để tích lũy tổng theo từng dòng

                                    @endphp
                                    @foreach ($top3MaxValues as $d)
                                        @php
                                            $label = $d['label'];
                                            $prefix = substr($label, 0, 6);
                                            $value = $data[$prefix][$day][$index1][$label][$time];
                                            $totalRow += $value;

                                        @endphp
                                    @endforeach
                                    <td class="text-xs px-0 py-0 border-r border-black">{{ $totalRow }}</td>
                                    @php
                                        $totalRow1[] = $totalRow;
                                        $totalColumn1Array = [];
                                        $colors = ['#e2efda', '#f0e2e2', '#c3e2ef'];
                                        $currentColorIndex = 0;
                                    @endphp

                                    @foreach ($top3MaxValues as $index_color => $d)
                                        @php

                                            $label = $d['label'];
                                            $prefix = substr($label, 0, 6);
                                            $value = $data[$prefix][$day][$index1][$label][$time];
                                            // dd($totalRow1);
                                        @endphp
                                        <td class="text-xs px-0 py-0 border-r border-black"
                                            style="background-color: {{ $colors[$currentColorIndex] }}">
                                            {{ $value }}
                                        </td>
                                        @php
                                            $percentage = $totalRow > 0 ? number_format(($value / $totalRow) * 100, 1) : 0;
                                            $currentColorIndex = ($currentColorIndex + 1) % count($colors);

                                        @endphp
                                        <td class="text-xs px-0 py-0 border-r border-black">{{ $percentage }}%</td>
                                    @endforeach




                                </tr>
                            @endforeach


                        </tbody>
                    </table>
                    {{-- show chart --}}
                    <div class="text-center mt-8 ">
                        <div id="chart-{{ $day }}" class=" flex justify-center items-center">
                            <canvas id="total_{{ $day }}_{{ $h }}" width="1152"
                                height="250"></canvas>


                        </div>
                    </div>

                    <br>
                    <br>
                    {{-- draw chart --}}
                    <script>
                        // Khai báo biến chart TOTAL
                        const table_{{ $day }}_{{ $h }} = document.getElementById(
                            'customers_{{ $day }}_{{ $h }}');
                        const labels1_{{ $day }}_{{ $h }} = [];


                        const date_{{ $day }}_{{ $h }} = [];
                        const numFooterRows_{{ $day }}_{{ $h }} = 1; // Số hàng ở phần cuối
                        const totalRows_{{ $day }}_{{ $h }} = table_{{ $day }}_{{ $h }}.rows
                            .length;
                        const startIndex_{{ $day }}_{{ $h }} = totalRows_{{ $day }}_{{ $h }} -
                            numFooterRows_{{ $day }}_{{ $h }};

                        // Khai báo biến chart1 với tên duy nhất dựa trên $day
                        const labels_{{ $day }}_{{ $h }} = [];
                        const dataTQC1_{{ $day }}_{{ $h }} = [];
                        const dataTQC2_{{ $day }}_{{ $h }} = [];
                        const dataTQC3_{{ $day }}_{{ $h }} = [];


                        // Lấy dữ liệu cho biểu đồ 1
                        for (let i = 1; i < (table_{{ $day }}_{{ $h }}.rows.length - 1); i++) {
                            const row = table_{{ $day }}_{{ $h }}.rows[i];

                            labels_{{ $day }}_{{ $h }}.push(row.cells[3].textContent);
                            dataTQC1_{{ $day }}_{{ $h }}.push(parseInt(row.cells[8].textContent));
                            dataTQC2_{{ $day }}_{{ $h }}.push(parseInt(row.cells[10].textContent));
                            dataTQC3_{{ $day }}_{{ $h }}.push(parseInt(row.cells[12].textContent));
                        }



                        // Vẽ biểu đồ 1
                        const ctx_{{ $day }}_{{ $h }} = document.getElementById(
                            'total_{{ $day }}_{{ $h }}');
                        const myChart_{{ $day }}_{{ $h }} = new Chart(
                            ctx_{{ $day }}_{{ $h }}, {
                                type: 'bar',
                                data: {
                                    labels: labels_{{ $day }}_{{ $h }},
                                    datasets: [{
                                            type: 'bar',
                                            label: 'ERROR1',
                                            data: dataTQC1_{{ $day }}_{{ $h }},
                                            borderColor: 'rgb(255, 99, 132)',
                                            backgroundColor: 'rgba(68,114,196,255)',
                                        },
                                        {
                                            type: 'bar',
                                            label: 'ERROR2',
                                            data: dataTQC2_{{ $day }}_{{ $h }},
                                            borderColor: 'rgb(255, 99, 132)',
                                            backgroundColor: 'rgba(237,125,49,255)',
                                        },
                                        {
                                            type: 'bar',
                                            label: 'ERROR3',
                                            data: dataTQC3_{{ $day }}_{{ $h }},
                                            borderColor: 'rgb(255, 99, 132)',
                                            backgroundColor: 'rgba(165,165,165,255)',
                                        },

                                    ],
                                },
                                options: {
                                    responsive: false,
                                    maintainAspectRatio: false,
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                        },
                                    },
                                },
                            });
                    </script>
                    @php
                        $h++;
                    @endphp
                @endforeach
            @endforeach
        @endif
    </div>


</body>

</html>
