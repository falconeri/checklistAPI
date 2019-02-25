<?php
/**
 * Created by PhpStorm.
 * Filename: ChecklistController.php
 * User: falconerialta@gmail.com
 * Date: 2019-02-25
 * Time: 14:20
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Illuminate\Pagination\Paginator;
use App\Checklist;

class ChecklistController extends Controller {
    public function index(Request $request) {
        $pageLimit = $request->input('page_limit', 10);
        $pageOffset = $request->input('page_offset', 0);

        Paginator::currentPageResolver(
            function () use ($pageOffset) {
                return $pageOffset;
            }
        );

        $checklists = Checklist::paginate($pageLimit, ["*"], "page_offset");

        $checklists->getCollection()->transform(
            function ($value) {
                return [
                    'type' => 'checklists',
                    'id' => $value->id,
                    'attributes' => $value,
                    'links' => [
                        'self' => route('checklist.show', ['checklistId' => $value->id])
                    ]
                ];
            }
        );

        return response()->json(
            [
                'meta' => [
                    'count' => $checklists->count(),
                    'total' => (int)$checklists->total(),
                ],
                'links' => [
                    'first' => $checklists->toArray()['first_page_url'],
                    'last' => $checklists->toArray()['last_page_url'],
                    'next' => $checklists->toArray()['next_page_url'],
                    'prev' => $checklists->toArray()['prev_page_url'],
                ],
                'data' => $checklists->all(),
            ], 200
        );

    }

    public function show($checklistId) {
        $checklist = Checklist::find($checklistId);

        if ($checklist) {
            return response()->json(
                [
                    'data' => [
                        'type' => 'checklists',
                        'id' => $checklist->id,
                        'attributes' => $checklist,
                        'links' => [
                            'self' => route('checklist.show', ['checklistId' => $checklist->id])
                        ]
                    ]
                ], 200
            );
        } else {
            return response()->json(
                [
                    'status' => '404',
                    'error' => 'Not Found',
                ], 404
            );
        }
    }

    public function store(Request $request) {
        $data = $request->input('data', array());
        $attributes = array();
        if (key_exists('attributes', $data)) {
            $attributes = $data['attributes'];
        }

        try {
            $checklist = Checklist::create($attributes);

            if ($checklist) {
                return response()->json(
                    [
                        'data' => [
                            'type' => 'checklists',
                            'id' => $checklist->id,
                            'attributes' => $checklist->find($checklist->id),
                            'links' => [
                                'self' => route('checklist.show', ['checklistId' => $checklist->id])
                            ]
                        ]
                    ], 200
                );
            } else {
                return response()->json(
                    [
                        'status' => '400',
                        'error' => 'Create Checklist Failed!',
                    ], 400
                );
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(
                [
                    'status' => '500',
                    'error' => $ex->getMessage(),
                ], 500
            );
        }
    }

    public function update(Request $request, $checklistId) {
        $checklist = Checklist::find($checklistId);
        if ($checklist) {
            $data = $request->input('data');

            $checklist->object_domain = $data['attributes']['object_domain'];
            $checklist->object_id = $data['attributes']['object_id'];
            $checklist->description = $data['attributes']['description'];
            $checklist->is_completed = $data['attributes']['is_completed'];
            $checklist->completed_at = $data['attributes']['completed_at'];

            try {
                $update = $checklist->save();
                if ($update) {
                    return response()->json(
                        [
                            'data' => [
                                'type' => 'checklists',
                                'id' => $checklist->id,
                                'attributes' => $checklist,
                                'links' => [
                                    'self' => route('checklist.show', ['checklistId' => $checklist->id])
                                ]
                            ]
                        ], 200
                    );
                } else {
                    return response()->json(
                        [
                            'status' => '400',
                            'error' => 'Update Checklist Failed!',
                        ], 400
                    );
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                return response()->json(
                    [
                        'status' => '500',
                        'error' => $ex->getMessage(),
                    ], 500
                );
            }
        } else {
            return response()->json(
                [
                    'status' => '404',
                    'error' => 'Not Found',
                ], 404
            );
        }
    }

    public function destroy($checklistId) {
        $checklist = Checklist::find($checklistId);

        if ($checklist) {
            try {
                $delete = $checklist->delete();

                if ($delete) {
                    return response(null, 204);
                } else {
                    return response()->json(
                        [
                            'status' => '400',
                            'error' => 'Delete Checklist Failed!',
                        ], 400
                    );
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                return response()->json(
                    [
                        'status' => '500',
                        'error' => $ex->getMessage(),
                    ], 500
                );
            }
        } else {
            return response()->json(
                [
                    'status' => '404',
                    'error' => 'Not Found',
                ], 404
            );
        }
    }
}