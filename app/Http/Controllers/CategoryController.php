<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CategoryGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    // ─── PUBLIC ──────────────────────────────────────────────────────────────

    /**
     * GET /api/categories
     * All active groups with their active categories (for homepage)
     */
    public function publicIndex()
    {
        $groups = CategoryGroup::with(['categories' => function ($q) {
            $q->where('is_active', true)->orderBy('sort_order');
        }])
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->get();

        return response()->json($groups);
    }

    // ─── ADMIN CRUD ───────────────────────────────────────────────────────────

    /** GET /api/admin/categories - all groups + all categories (incl. inactive) */
    public function adminIndex()
    {
        return response()->json(
            CategoryGroup::with(['categories' => fn($q) => $q->orderBy('sort_order')])
                ->orderBy('sort_order')->get()
        );
    }

    // ── GROUPS ────────────────────────────────────────────
    public function storeGroup(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'sort_order' => 'integer',
            'is_active'  => 'boolean',
        ]);
        $data['slug'] = Str::slug($data['name']);
        $group = CategoryGroup::create($data);
        return response()->json($group, 201);
    }

    public function updateGroup(Request $request, int $id)
    {
        $group = CategoryGroup::findOrFail($id);
        $data  = $request->validate([
            'name'       => 'sometimes|string|max:255',
            'sort_order' => 'sometimes|integer',
            'is_active'  => 'sometimes|boolean',
        ]);
        if (isset($data['name'])) $data['slug'] = Str::slug($data['name']);
        $group->update($data);
        return response()->json($group);
    }

    public function destroyGroup(int $id)
    {
        CategoryGroup::findOrFail($id)->delete();
        return response()->json(['message' => 'Group deleted']);
    }

    // ── CATEGORIES ────────────────────────────────────────
    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'category_group_id' => 'required|integer|exists:category_groups,id',
            'name'              => 'required|string|max:255',
            'subtitle'          => 'nullable|string|max:255',
            'image_url'         => 'nullable|string|max:1000',
            'media_type_filter' => 'nullable|string|max:100',
            'sort_order'        => 'integer',
            'is_active'         => 'boolean',
        ]);
        $cat = Category::create($data);
        return response()->json($cat, 201);
    }

    public function updateCategory(Request $request, int $id)
    {
        $cat  = Category::findOrFail($id);
        $data = $request->validate([
            'category_group_id' => 'sometimes|integer|exists:category_groups,id',
            'name'              => 'sometimes|string|max:255',
            'subtitle'          => 'nullable|string|max:255',
            'image_url'         => 'nullable|string|max:1000',
            'media_type_filter' => 'nullable|string|max:100',
            'sort_order'        => 'sometimes|integer',
            'is_active'         => 'sometimes|boolean',
        ]);
        $cat->update($data);
        return response()->json($cat);
    }

    public function destroyCategory(int $id)
    {
        Category::findOrFail($id)->delete();
        return response()->json(['message' => 'Category deleted']);
    }
}
