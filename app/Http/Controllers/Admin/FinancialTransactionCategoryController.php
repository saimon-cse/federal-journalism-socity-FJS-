<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialTransactionCategory;
use Illuminate\Http\Request;

class FinancialTransactionCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage-financial-categories');
    }

    public function index()
    {
        $categories = FinancialTransactionCategory::with('parentCategory')->latest()->paginate(10);
        return view('admin.financial_transaction_categories.index', compact('categories'));
    }

    public function create()
    {
        $parentCategories = FinancialTransactionCategory::whereNull('parent_category_id')->orderBy('name')->get();
        return view('admin.financial_transaction_categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:financial_transaction_categories,name',
            'type' => 'required|in:income,expense',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'parent_category_id' => 'nullable|exists:financial_transaction_categories,id',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        FinancialTransactionCategory::create($data);
        return redirect()->route('admin.financial-transaction-categories.index')->with('success', 'Category created.');
    }

    public function edit(FinancialTransactionCategory $financialTransactionCategory)
    {
        $parentCategories = FinancialTransactionCategory::whereNull('parent_category_id')
            ->where('id', '!=', $financialTransactionCategory->id) // Cannot be its own parent
            ->orderBy('name')->get();
        return view('admin.financial_transaction_categories.edit', compact('financialTransactionCategory', 'parentCategories'));
    }

    public function update(Request $request, FinancialTransactionCategory $financialTransactionCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:financial_transaction_categories,name,' . $financialTransactionCategory->id,
            'type' => 'required|in:income,expense',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'parent_category_id' => 'nullable|exists:financial_transaction_categories,id',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        if ($request->parent_category_id == $financialTransactionCategory->id) { // Prevent self-parenting
            $data['parent_category_id'] = null;
        }

        $financialTransactionCategory->update($data);
        return redirect()->route('admin.financial-transaction-categories.index')->with('success', 'Category updated.');
    }

    public function destroy(FinancialTransactionCategory $financialTransactionCategory)
    {
        // Check if category has subcategories or is used in ledger entries
        if ($financialTransactionCategory->subCategories()->exists() || $financialTransactionCategory->financialLedgers()->exists()) {
            return redirect()->route('admin.financial-transaction-categories.index')->with('error', 'Cannot delete category. It has subcategories or is in use.');
        }
        $financialTransactionCategory->delete();
        return redirect()->route('admin.financial-transaction-categories.index')->with('success', 'Category deleted.');
    }
}
