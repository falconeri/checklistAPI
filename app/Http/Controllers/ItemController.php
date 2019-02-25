<?php
/**
 * Created by PhpStorm.
 * Filename: ItemController.php
 * User: falconerialta@gmail.com
 * Date: 2019-02-25
 * Time: 22:50
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use App\Checklist;
use App\Item;

class ItemController extends Controller {
    public function index(Request $request, $checklistId) {
        $checklist = Checklist::find($checklistId);

        if ($checklist) {
            $pageLimit = $request->input('page_limit', 10);
            $pageOffset = $request->input('page_offset', 1);

            Paginator::currentPageResolver(
                function () use ($pageOffset) {
                    return $pageOffset;
                }
            );

            $items = Item::paginate($pageLimit, ["*"], "page_offset");
            $checklist['items'] = $items->all();

            return response()->json(
                [
                    'meta' => [
                        'count' => $items->count(),
                        'total' => (int)$items->total(),
                    ],
                    'links' => [
                        'first' => $items->toArray()['first_page_url'],
                        'last' => $items->toArray()['last_page_url'],
                        'next' => $items->toArray()['next_page_url'],
                        'prev' => $items->toArray()['prev_page_url'],
                    ],
                    'data' => [
                        'type' => 'checklists',
                        'id' => $checklist->id,
                        'attributes' => $checklist,
                        'links' => [
                            'self' => route('item.index', ['checklistId' => $checklist->id])
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

    public function show($checklistId, $itemId) {
        $checklist = Checklist::find($checklistId);

        if ($checklist) {
            $item = Item::find($itemId);
            if ($item) {
                return response()->json(
                    [
                        'data' => [
                            'type' => 'checklists',
                            'id' => $item->id,
                            'attributes' => $item->find($item->id),
                            'links' => [
                                'self' => route('item.show', ['checklistId' => $checklist->id, 'itemId' => $item->id])
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
        } else {
            return response()->json(
                [
                    'status' => '404',
                    'error' => 'Not Found',
                ], 404
            );
        }
    }

    public function store(Request $request, $checklistId) {
        $checklist = Checklist::find($checklistId);

        if ($checklist) {
            $data = $request->input('data', array());
            $attributes = array();
            if (key_exists('attributes', $data)) {
                $attributes = $data['attributes'];
            }
            $attributes['checklist_id'] = $checklist->id;

            try {
                $item = Item::create($attributes);

                if ($item) {
                    return response()->json(
                        [
                            'data' => [
                                'type' => 'checklists',
                                'id' => $item->id,
                                'attributes' => $item->find($item->id),
                                'links' => [
                                    'self' => route('item.show', ['checklistId' => $checklist->id, 'itemId' => $item->id])
                                ]
                            ]
                        ], 200
                    );
                } else {
                    return response()->json(
                        [
                            'status' => '400',
                            'error' => 'Create Checklist Item Failed!',
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

    public function update(Request $request, $checklistId, $itemId) {
        $checklist = Checklist::find($checklistId);
        if ($checklist) {
            $item = Item::find($itemId);
            if ($item) {
                $data = $request->input('data');

                $item->description = $data['attributes']['description'];
                $item->due = $data['attributes']['due'];
                $item->urgency = $data['attributes']['urgency'];

                try {
                    $update = $item->save();
                    if ($update) {
                        return response()->json(
                            [
                                'data' => [
                                    'type' => 'checklists',
                                    'id' => $item->id,
                                    'attributes' => $item,
                                    'links' => [
                                        'self' => route('item.show', ['checklistId' => $checklist->id, 'itemId' => $item->id])
                                    ]
                                ]
                            ], 200
                        );
                    } else {
                        return response()->json(
                            [
                                'status' => '400',
                                'error' => 'Update Checklist item Failed!',
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
        } else {
            return response()->json(
                [
                    'status' => '404',
                    'error' => 'Not Found',
                ], 404
            );
        }
    }

    public function destroy($checklistId, $itemId) {
        $checklist = Checklist::find($checklistId);

        if ($checklist) {
            $item = Item::find($itemId);
            if ($item) {
                try {
                    $delete = $item->delete();

                    if ($delete) {
                        return response(null, 204);
                    } else {
                        return response()->json(
                            [
                                'status' => '400',
                                'error' => 'Delete Checklist item Failed!',
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
        } else {
            return response()->json(
                [
                    'status' => '404',
                    'error' => 'Not Found',
                ], 404
            );
        }
    }

    public function complete(Request $request) {
        $data = $request->input('data', array());
        $item_ids = array();
        foreach ($data as $items) {
            if (key_exists('item_id', $items)) {
                $item_ids[] = $items['item_id'];
            }
        }

        try {
            Item::whereIn('id', $item_ids)->update(['is_completed' => true]);

            $items = Item::select('id', 'is_completed', 'checklist_id')->whereIn('id', $item_ids)->get();
            $items = $items->each(
                function ($i, $k) {
                    $i->makeVisible(['checklist_id']);
                }
            );

            return response()->json(
                [
                    'data' => $items
                ], 200
            );

        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(
                [
                    'status' => '500',
                    'error' => $ex->getMessage(),
                ], 500
            );
        }
    }

    public function incomplete(Request $request) {
        $data = $request->input('data', array());
        $item_ids = array();
        foreach ($data as $items) {
            if (key_exists('item_id', $items)) {
                $item_ids[] = $items['item_id'];
            }
        }

        try {
            Item::whereIn('id', $item_ids)->update(['is_completed' => false]);

            $items = Item::select('id', 'is_completed', 'checklist_id')->whereIn('id', $item_ids)->get();
            $items = $items->each(
                function ($i, $k) {
                    $i->makeVisible(['checklist_id']);
                }
            );

            return response()->json(
                [
                    'data' => $items
                ], 200
            );

        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(
                [
                    'status' => '500',
                    'error' => $ex->getMessage(),
                ], 500
            );
        }
    }
}