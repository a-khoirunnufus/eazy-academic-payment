<?php

namespace App\Services\ReRegistInvoice;

use Illuminate\Support\Facades\Log;
use App\Models\Payment\ComponentDetail;

class GenerateTreeComplete {

    private $paths;
    private $registrants;

    public function __construct($paths, $registrants)
    {
        $this->paths = $paths;
        $this->registrants = $registrants;
    }

    public function generate()
    {
        $raw_tree = (new GenerateTreeByPaths($this->paths))->generate();

        $tree = array();

        foreach ($raw_tree as $faculty) {
            $faculty_id = $faculty['data']['obj_id'];
            $filtered_by_faculty = array_values(array_filter($this->registrants, function($item) use($faculty_id) {
                return $item->faculty_id == $faculty_id;
            }));

            $new_faculty_item = $faculty;
            $new_faculty_item['children'] = [];
            $new_faculty_item_children = array();

            foreach ($faculty['children'] as $studyprogram) {
                $studyprogram_id = $studyprogram['data']['obj_id'];
                $filtered_by_studyprogram = array_values(array_filter($filtered_by_faculty, function($item) use($studyprogram_id) {
                    return $item->studyprogram_id == $studyprogram_id;
                }));

                $new_studyprogram_item = $studyprogram;
                $new_studyprogram_item['children'] = [];
                $new_studyprogram_item_children = array();

                foreach ($studyprogram['children'] as $path_idx => $path) {
                    $path_id = $path['data']['obj_id'];
                    $filtered_by_path = array_values(array_filter($filtered_by_studyprogram, function($item) use($path_id) {
                        return $item->registration_path_id == $path_id;
                    }));

                    $new_path_item = $path;
                    $new_path_item['children'] = [];
                    $new_path_item_children = array();

                    foreach ($path['children'] as $period_idx => $period) {
                        $period_id = $period['data']['obj_id'];
                        $filtered_by_period = array_values(array_filter($filtered_by_path, function($item) use($period_id) {
                            return $item->registration_period_id == $period_id;
                        }));

                        $new_period_item = $period;
                        $new_period_item['children'] = [];
                        $new_period_item_children = array();

                        foreach ($period['children'] as $lecture_type_idx => $lecture_type) {
                            $lecture_type_id = $lecture_type['data']['obj_id'];
                            $filtered_by_lecture_type = array_values(array_filter($filtered_by_period, function($item) use($lecture_type_id) {
                                return $item->lecture_type_id == $lecture_type_id;
                            }));

                            $new_lecture_type_item = $lecture_type;
                            $new_lecture_type_item['children'] = [];
                            $new_lecture_type_item['text'] = 'Jenis Perkuliahan: '.$filtered_by_lecture_type[0]->lecture_type_name;
                            $new_lecture_type_item_status_generated = CountGeneratedInvoice::count($filtered_by_lecture_type);
                            $new_lecture_type_item_status_invoice_component = ComponentDetail::where([
                                    ['mlt_id', '=', $lecture_type_id],
                                    ['period_id', '=', $period_id],
                                    ['path_id', '=', $path_id],
                                    ['mma_id', '=', $studyprogram_id],
                                ])->get()->count() > 0 ? 'defined' : 'not_defined';
                            // Log::debug(
                            //     ComponentDetail::where([
                            //         ['mlt_id', '=', $lecture_type_id],
                            //         ['period_id', '=', $period_id],
                            //         ['path_id', '=', $path_id],
                            //         ['mma_id', '=', $studyprogram_id],
                            //     ])->get()
                            // );
                            $new_lecture_type_item['data'] = [
                                ...$new_lecture_type_item['data'],
                                'status_generated' => $new_lecture_type_item_status_generated,
                                'status_invoice_component' => $new_lecture_type_item_status_invoice_component,
                            ];
                            $new_lecture_type_item['state'] = [
                                'disabled' => $new_lecture_type_item_status_generated['status'] == 'done_generated' ? true : false,
                            ];
                            $new_period_item_children[] = $new_lecture_type_item;
                        }

                        $new_period_item['text'] = 'Periode: '.$filtered_by_period[0]->registration_period_name;
                        $new_period_item_status_generated = CountGeneratedInvoice::count($filtered_by_period);
                        $new_period_item['data'] = [
                            ...$new_period_item['data'],
                            'status_generated' => $new_period_item_status_generated,
                        ];
                        $new_period_item['state'] = [
                            'disabled' => $new_period_item_status_generated['status'] == 'done_generated' ? true : false,
                        ];
                        $new_period_item['children'] = $new_period_item_children;
                        $new_path_item_children[] = $new_period_item;
                    }

                    $new_path_item['text'] = 'Jalur: '.$filtered_by_path[0]->registration_path_name;
                    $new_path_item_status_generated = CountGeneratedInvoice::count($filtered_by_path);
                    $new_path_item['data'] = [
                        ...$new_path_item['data'],
                        'status_generated' => $new_path_item_status_generated,
                    ];
                    $new_path_item['state'] = [
                        'disabled' => $new_path_item_status_generated['status'] == 'done_generated' ? true : false,
                    ];
                    $new_path_item['children'] = $new_path_item_children;
                    $new_studyprogram_item_children[] = $new_path_item;
                }

                $new_studyprogram_item['text'] = 'Program Studi: '.strtoupper($filtered_by_studyprogram[0]->studyprogram_type).' '.$filtered_by_studyprogram[0]->studyprogram_name;
                $new_studyprogram_item_status_generated = CountGeneratedInvoice::count($filtered_by_studyprogram);
                $new_studyprogram_item['data'] = [
                    ...$new_studyprogram_item['data'],
                    'status_generated' => $new_studyprogram_item_status_generated,
                ];
                $new_studyprogram_item['state'] = [
                    'disabled' => $new_studyprogram_item_status_generated['status'] == 'done_generated' ? true : false,
                ];
                $new_studyprogram_item['children'] = $new_studyprogram_item_children;
                $new_faculty_item_children[] = $new_studyprogram_item;
            }

            $new_faculty_item['text'] = 'Fakultas: '.$filtered_by_faculty[0]->faculty_name;
            $new_faculty_item_status_generated = CountGeneratedInvoice::count($filtered_by_faculty);
            $new_faculty_item['data'] = [
                ...$new_faculty_item['data'],
                'status_generated' => $new_faculty_item_status_generated,
            ];
            $new_faculty_item['state'] = [
                'disabled' => $new_faculty_item_status_generated['status'] == 'done_generated' ? true : false,
            ];
            $new_faculty_item['children'] = $new_faculty_item_children;
            $tree[] = $new_faculty_item;
        }

        return $tree;
    }

    public function generateByFaculty()
    {
        $raw_tree = (new GenerateTreeByPaths($this->paths))->generate();

        $tree = array();

        foreach ($raw_tree as $studyprogram) {
            $studyprogram_id = $studyprogram['data']['obj_id'];
            $filtered_by_studyprogram = array_values(array_filter($this->registrants, function($item) use($studyprogram_id) {
                return $item->studyprogram_id == $studyprogram_id;
            }));

            $new_studyprogram_item = $studyprogram;
            $new_studyprogram_item['children'] = [];
            $new_studyprogram_item_children = array();

            foreach ($studyprogram['children'] as $path_idx => $path) {
                $path_id = $path['data']['obj_id'];
                $filtered_by_path = array_values(array_filter($filtered_by_studyprogram, function($item) use($path_id) {
                    return $item->registration_path_id == $path_id;
                }));

                $new_path_item = $path;
                $new_path_item['children'] = [];
                $new_path_item_children = array();

                foreach ($path['children'] as $period_idx => $period) {
                    $period_id = $period['data']['obj_id'];
                    $filtered_by_period = array_values(array_filter($filtered_by_path, function($item) use($period_id) {
                        return $item->registration_period_id == $period_id;
                    }));

                    $new_period_item = $period;
                    $new_period_item['children'] = [];
                    $new_period_item_children = array();

                    foreach ($period['children'] as $lecture_type_idx => $lecture_type) {
                        $lecture_type_id = $lecture_type['data']['obj_id'];
                        $filtered_by_lecture_type = array_values(array_filter($filtered_by_period, function($item) use($lecture_type_id) {
                            return $item->lecture_type_id == $lecture_type_id;
                        }));

                        $new_lecture_type_item = $lecture_type;
                        $new_lecture_type_item['children'] = [];
                        $new_lecture_type_item['text'] = 'Jenis Perkuliahan: '.$filtered_by_lecture_type[0]->lecture_type_name;
                        $new_lecture_type_item_status_generated = CountGeneratedInvoice::count($filtered_by_lecture_type);
                        $new_lecture_type_item_status_invoice_component = ComponentDetail::where([
                                ['mlt_id', '=', $lecture_type_id],
                                ['period_id', '=', $period_id],
                                ['path_id', '=', $path_id],
                                ['mma_id', '=', $studyprogram_id],
                            ])->get()->count() > 0 ? 'defined' : 'not_defined';
                        $new_lecture_type_item['data'] = [
                            ...$new_lecture_type_item['data'],
                            'status_generated' => $new_lecture_type_item_status_generated,
                            'status_invoice_component' => $new_lecture_type_item_status_invoice_component,
                        ];
                        $new_lecture_type_item['state'] = [
                            'disabled' => $new_lecture_type_item_status_generated['status'] == 'done_generated' ? true : false,
                        ];
                        $new_period_item_children[] = $new_lecture_type_item;
                    }

                    $new_period_item['text'] = 'Periode: '.$filtered_by_period[0]->registration_period_name;
                    $new_period_item_status_generated = CountGeneratedInvoice::count($filtered_by_period);
                    $new_period_item['data'] = [
                        ...$new_period_item['data'],
                        'status_generated' => $new_period_item_status_generated,
                    ];
                    $new_period_item['state'] = [
                        'disabled' => $new_period_item_status_generated['status'] == 'done_generated' ? true : false,
                    ];
                    $new_period_item['children'] = $new_period_item_children;
                    $new_path_item_children[] = $new_period_item;
                }

                $new_path_item['text'] = 'Jalur: '.$filtered_by_path[0]->registration_path_name;
                $new_path_item_status_generated = CountGeneratedInvoice::count($filtered_by_path);
                $new_path_item['data'] = [
                    ...$new_path_item['data'],
                    'status_generated' => $new_path_item_status_generated,
                ];
                $new_path_item['state'] = [
                    'disabled' => $new_path_item_status_generated['status'] == 'done_generated' ? true : false,
                ];
                $new_path_item['children'] = $new_path_item_children;
                $new_studyprogram_item_children[] = $new_path_item;
            }

            $new_studyprogram_item['text'] = 'Program Studi: '.strtoupper($filtered_by_studyprogram[0]->studyprogram_type).' '.$filtered_by_studyprogram[0]->studyprogram_name;
            $new_studyprogram_item_status_generated = CountGeneratedInvoice::count($filtered_by_studyprogram);
            $new_studyprogram_item['data'] = [
                ...$new_studyprogram_item['data'],
                'status_generated' => $new_studyprogram_item_status_generated,
            ];
            $new_studyprogram_item['state'] = [
                'disabled' => $new_studyprogram_item_status_generated['status'] == 'done_generated' ? true : false,
            ];
            $new_studyprogram_item['children'] = $new_studyprogram_item_children;
            $tree[] = $new_studyprogram_item;
        }

        return $tree;
    }

    public function generateByStudyprogram()
    {
        $raw_tree = (new GenerateTreeByPaths($this->paths))->generate();

        $tree = array();

        foreach ($raw_tree as $path) {
            $path_id = $path['data']['obj_id'];
            $filtered_by_path = array_values(array_filter($this->registrants, function($item) use($path_id) {
                return $item->registration_path_id == $path_id;
            }));

            $new_path_item = $path;
            $new_path_item['children'] = [];
            $new_path_item_children = array();

            foreach ($path['children'] as $period_idx => $period) {
                $period_id = $period['data']['obj_id'];
                $filtered_by_period = array_values(array_filter($filtered_by_path, function($item) use($period_id) {
                    return $item->registration_period_id == $period_id;
                }));

                $new_period_item = $period;
                $new_period_item['children'] = [];
                $new_period_item_children = array();

                foreach ($period['children'] as $lecture_type_idx => $lecture_type) {
                    $lecture_type_id = $lecture_type['data']['obj_id'];
                    $filtered_by_lecture_type = array_values(array_filter($filtered_by_period, function($item) use($lecture_type_id) {
                        return $item->lecture_type_id == $lecture_type_id;
                    }));

                    $new_lecture_type_item = $lecture_type;
                    $new_lecture_type_item['children'] = [];
                    $new_lecture_type_item['text'] = 'Jenis Perkuliahan: '.$filtered_by_lecture_type[0]->lecture_type_name;
                    $new_lecture_type_item_status_generated = CountGeneratedInvoice::count($filtered_by_lecture_type);
                    $component_details = ComponentDetail::where([
                            ['mlt_id', '=', $lecture_type_id],
                            ['period_id', '=', $period_id],
                            ['path_id', '=', $path_id],
                            ['mma_id', '=', $filtered_by_lecture_type[0]->studyprogram_id]
                        ])->get();
                    $new_lecture_type_item_status_invoice_component = count($component_details) > 0 ? 'defined' : 'not_defined';
                    $new_lecture_type_item['data'] = [
                        ...$new_lecture_type_item['data'],
                        'status_generated' => $new_lecture_type_item_status_generated,
                        'status_invoice_component' => $new_lecture_type_item_status_invoice_component,
                    ];
                    $new_lecture_type_item['state'] = [
                        'disabled' => $new_lecture_type_item_status_generated['status'] == 'done_generated' ? true : false,
                    ];
                    $new_period_item_children[] = $new_lecture_type_item;
                }

                $new_period_item['text'] = 'Periode: '.$filtered_by_period[0]->registration_period_name;
                $new_period_item_status_generated = CountGeneratedInvoice::count($filtered_by_period);
                $new_period_item['data'] = [
                    ...$new_period_item['data'],
                    'status_generated' => $new_period_item_status_generated,
                ];
                $new_period_item['state'] = [
                    'disabled' => $new_period_item_status_generated['status'] == 'done_generated' ? true : false,
                ];
                $new_period_item['children'] = $new_period_item_children;
                $new_path_item_children[] = $new_period_item;
            }

            $new_path_item['text'] = 'Jalur: '.$filtered_by_path[0]->registration_path_name;
            $new_path_item_status_generated = CountGeneratedInvoice::count($filtered_by_path);
            $new_path_item['data'] = [
                ...$new_path_item['data'],
                'status_generated' => $new_path_item_status_generated,
            ];
            $new_path_item['state'] = [
                'disabled' => $new_path_item_status_generated['status'] == 'done_generated' ? true : false,
            ];
            $new_path_item['children'] = $new_path_item_children;
            $tree[] = $new_path_item;
        }

        return $tree;
    }
}
