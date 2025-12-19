<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhatsAppTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WhatsAppTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = WhatsAppTemplate::query();

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $templates = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get stats
        $stats = [
            'total' => WhatsAppTemplate::count(),
            'active' => WhatsAppTemplate::where('is_active', true)->count(),
            'inactive' => WhatsAppTemplate::where('is_active', false)->count(),
            'default' => WhatsAppTemplate::where('is_default', true)->count(),
        ];

        return view('admin.whatsapp.templates.index', compact('templates', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.whatsapp.templates.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:invoice,reminder,suspension,voucher,custom',
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string',
            'variables' => 'nullable|string',
            'is_default' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // If setting as default, remove default flag from other templates of same type
        if ($request->is_default) {
            WhatsAppTemplate::where('type', $request->type)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        // Parse variables
        $variables = [];
        if ($request->filled('variables')) {
            $variables = array_map('trim', explode(',', $request->variables));
            $variables = array_filter($variables);
        }

        WhatsAppTemplate::create([
            'name' => $request->name,
            'type' => $request->type,
            'subject' => $request->subject,
            'content' => $request->content,
            'variables' => $variables,
            'is_default' => $request->is_default ?? false,
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.whatsapp.templates.index')
            ->with('success', 'Template WhatsApp berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(WhatsAppTemplate $template)
    {
        return view('admin.whatsapp.templates.show', compact('template'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WhatsAppTemplate $template)
    {
        return view('admin.whatsapp.templates.edit', compact('template'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WhatsAppTemplate $template)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:invoice,reminder,suspension,voucher,custom',
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string',
            'variables' => 'nullable|string',
            'is_default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // If setting as default, remove default flag from other templates of same type
        if ($request->is_default && !$template->is_default) {
            WhatsAppTemplate::where('type', $request->type)
                ->where('id', '!=', $template->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        // Parse variables
        $variables = [];
        if ($request->filled('variables')) {
            $variables = array_map('trim', explode(',', $request->variables));
            $variables = array_filter($variables);
        }

        $template->update([
            'name' => $request->name,
            'type' => $request->type,
            'subject' => $request->subject,
            'content' => $request->content,
            'variables' => $variables,
            'is_default' => $request->is_default ?? false,
            'is_active' => $request->is_active ?? false,
        ]);

        return redirect()
            ->route('admin.whatsapp.templates.index')
            ->with('success', 'Template WhatsApp berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WhatsAppTemplate $template)
    {
        // Prevent deletion of default templates
        if ($template->is_default) {
            return back()->with('error', 'Template default tidak dapat dihapus');
        }

        $template->delete();

        return redirect()
            ->route('admin.whatsapp.templates.index')
            ->with('success', 'Template WhatsApp berhasil dihapus');
    }

    /**
     * Toggle template active status
     */
    public function toggle(WhatsAppTemplate $template)
    {
        $template->update([
            'is_active' => !$template->is_active
        ]);

        $status = $template->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Template berhasil {$status}");
    }

    /**
     * Set template as default for its type
     */
    public function setDefault(WhatsAppTemplate $template)
    {
        // Remove default flag from other templates of same type
        WhatsAppTemplate::where('type', $template->type)
            ->where('id', '!=', $template->id)
            ->update(['is_default' => false]);

        // Set this template as default
        $template->update(['is_default' => true]);

        return back()->with('success', 'Template berhasil dijadikan default');
    }

    /**
     * Preview template with sample data
     */
    public function preview(Request $request)
    {
        $template = WhatsAppTemplate::findOrFail($request->template_id);

        $sampleData = [
            'nama' => 'John Doe',
            'invoice' => 'INV-2024-001',
            'paket' => 'Internet Premium 20Mbps',
            'amount' => '150000',
            'due_date' => '15 Desember 2024',
            'app_name' => config('app.name', 'Gembok Network'),
        ];

        $preview = $template->render($sampleData);

        return response()->json([
            'success' => true,
            'preview' => $preview
        ]);
    }
}
