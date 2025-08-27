<?php

namespace App\Http\Controllers;

use App\Announcement;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('part_time_auth');
        
        // Only admin and managers can create/edit announcements
        $this->middleware(function ($request, $next) {
            if (!in_array(auth()->user()->roles, [1, 2])) {
                abort(403, '権限がありません。');
            }
            return $next($request);
        })->only(['create', 'store', 'edit', 'update', 'destroy', 'requestApproval', 'approve', 'reject', 'publish']);
    }

    /**
     * Display a listing of the announcements.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $announcements = Announcement::with(['creator', 'approver'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('announcements.index', compact('announcements'));
    }

    /**
     * Show the form for creating a new announcement.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $schoolBuildings = \App\SchoolBuilding::select('id', 'name', 'name_short')
            ->orderBy('name')
            ->get();

        return view('announcements.create', compact('schoolBuildings'));
    }

    /**
     * Store a newly created announcement in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'distribution_targets' => 'required|array|min:1',
            'action' => 'required|in:save_draft,request_approval'
        ], [
            'title.required' => 'タイトルは必須です。',
            'title.max' => 'タイトルは255文字以内で入力してください。',
            'content.required' => '内容は必須です。',
            'distribution_targets.required' => '配信先を選択してください。',
            'distribution_targets.min' => '配信先を1つ以上選択してください。',
            'action.required' => '保存方法を選択してください。',
            'action.in' => '無効な保存方法です。'
        ]);

        $announcement = new Announcement();
        $announcement->title = $validated['title'];
        $announcement->content = $this->sanitizeHtml($validated['content']);
        $announcement->distribution_targets = $validated['distribution_targets'];
        $announcement->created_by = Auth::id();

        // アクションに応じてステータスを設定
        if ($validated['action'] === 'save_draft') {
            $announcement->status = Announcement::STATUS_DRAFT;
            $message = 'お知らせを下書きとして保存しました。';
        } else {
            $announcement->status = Announcement::STATUS_PENDING;
            $message = 'お知らせを承認申請しました。';
        }

        $announcement->save();

        return redirect()->route('announcements.index')
            ->with('flash_message', $message);
    }

    /**
     * Display the specified announcement.
     *
     * @param  \App\Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function show(Announcement $announcement)
    {
        $announcement->load(['creator', 'approver']);
        return view('announcements.show', compact('announcement'));
    }

    /**
     * Show the form for editing the specified announcement.
     *
     * @param  \App\Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function edit(Announcement $announcement)
    {
        // 公開済みのお知らせは編集できないが、他は編集可能
        if ($announcement->status === 'pending') {
            return redirect()->route('announcements.index')
                ->with('error_message', '承認待ちのお知らせは編集できません。');
        }

        $schoolBuildings = \App\SchoolBuilding::select('id', 'name', 'name_short')
            ->orderBy('name')
            ->get();

        return view('announcements.edit', compact('announcement', 'schoolBuildings'));
    }

    /**
     * Update the specified announcement in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Announcement $announcement)
    {
        // 承認待ちのお知らせは編集できない
        if ($announcement->status === 'pending') {
            return redirect()->route('announcements.index')
                ->with('error_message', '承認待ちのお知らせは編集できません。');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'distribution_targets' => 'required|array|min:1',
            'action' => 'required|in:save_draft,request_approval'
        ], [
            'title.required' => 'タイトルは必須です。',
            'title.max' => 'タイトルは255文字以内で入力してください。',
            'content.required' => '内容は必須です。',
            'distribution_targets.required' => '配信先を選択してください。',
            'distribution_targets.min' => '配信先を1つ以上選択してください。',
            'action.required' => '保存方法を選択してください。',
            'action.in' => '無効な保存方法です。'
        ]);

        $announcement->title = $validated['title'];
        $announcement->content = $this->sanitizeHtml($validated['content']);
        $announcement->distribution_targets = $validated['distribution_targets'];

        // 公開済みの場合はステータスを変更しない
        if ($announcement->status !== 'published') {
            // アクションに応じてステータスを更新
            if ($validated['action'] === 'save_draft') {
                $announcement->status = Announcement::STATUS_DRAFT;
                $announcement->approved_by = null;
                $announcement->approved_at = null;
                $announcement->approval_comment = null;
                $message = 'お知らせを下書きとして保存しました。';
            } else {
                $announcement->status = Announcement::STATUS_PENDING;
                $announcement->approved_by = null;
                $announcement->approved_at = null;
                $announcement->approval_comment = null;
                $message = 'お知らせを承認申請しました。';
            }
        } else {
            $message = 'お知らせを更新しました。';
        }

        $announcement->save();

        return redirect()->route('announcements.index')
            ->with('flash_message', $message);
    }

    /**
     * Remove the specified announcement from storage.
     *
     * @param  \App\Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return redirect()->route('announcements.index')
            ->with('flash_message', 'お知らせを削除しました。');
    }

    /**
     * Request approval for announcement
     *
     * @param  \App\Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function requestApproval(Announcement $announcement)
    {
        if (!$announcement->isDraft()) {
            return redirect()->back()->with('error_message', '下書き状態のお知らせのみ承認申請できます。');
        }

        $announcement->status = Announcement::STATUS_PENDING;
        $announcement->save();

        return redirect()->route('announcements.index')
            ->with('flash_message', 'お知らせの承認申請を行いました。');
    }

    /**
     * Approve announcement
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function approve(Request $request, Announcement $announcement)
    {
        if (!$announcement->isPending()) {
            return redirect()->back()->with('error_message', '承認待ち状態のお知らせのみ承認できます。');
        }

        $announcement->status = Announcement::STATUS_APPROVED;
        $announcement->approved_by = Auth::id();
        $announcement->approved_at = now();
        $announcement->approval_comment = $request->input('approval_comment');
        $announcement->save();

        return redirect()->route('announcements.index')
            ->with('flash_message', 'お知らせを承認しました。');
    }

    /**
     * Reject announcement
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function reject(Request $request, Announcement $announcement)
    {
        if (!$announcement->isPending()) {
            return redirect()->back()->with('error_message', '承認待ち状態のお知らせのみ却下できます。');
        }

        $announcement->status = Announcement::STATUS_DRAFT;
        $announcement->approved_by = null;
        $announcement->approved_at = null;
        $announcement->approval_comment = $request->input('approval_comment');
        $announcement->save();

        return redirect()->route('announcements.index')
            ->with('flash_message', 'お知らせを却下しました。下書きとして保存されました。');
    }

    /**
     * Publish announcement
     *
     * @param  \App\Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function publish(Announcement $announcement)
    {
        if (!$announcement->isApproved()) {
            return redirect()->back()->with('error_message', '承認済み状態のお知らせのみ公開できます。');
        }

        $announcement->status = Announcement::STATUS_PUBLISHED;
        $announcement->is_published = true;
        $announcement->published_at = now();
        $announcement->save();

        return redirect()->route('announcements.index')
            ->with('flash_message', 'お知らせを公開しました。');
    }

    /**
     * Unpublish announcement
     *
     * @param  \App\Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function unpublish(Announcement $announcement)
    {
        if (!$announcement->isPublished()) {
            return redirect()->back()->with('error_message', '公開済み状態のお知らせのみ未公開にできます。');
        }

        $announcement->status = Announcement::STATUS_APPROVED;
        $announcement->is_published = false;
        $announcement->published_at = null;
        $announcement->save();

        return redirect()->route('announcements.index')
            ->with('flash_message', 'お知らせを未公開に変更しました。');
    }

    /**
     * Upload image for announcements
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImage(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
            ]);

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                
                // ファイル名をサニタイズ
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '_' . uniqid() . '.' . $extension;
                
                // アップロードディレクトリのパス
                $uploadPath = public_path('uploads/announcements');
                
                // ディレクトリが存在しない場合は作成
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                // ファイルを移動
                if ($file->move($uploadPath, $filename)) {
                    // サーバーとローカルの両方で動作する絶対パスを生成
                    $baseUrl = request()->getSchemeAndHttpHost();
                    $url = $baseUrl . '/uploads/announcements/' . $filename;
                    
                    return response()->json([
                        'location' => $url,
                        'success' => true
                    ]);
                } else {
                    return response()->json([
                        'error' => 'ファイルの保存に失敗しました。'
                    ], 500);
                }
            }

            return response()->json([
                'error' => 'ファイルが選択されていません。'
            ], 400);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'アップロードエラー: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sanitize HTML content
     *
     * @param  string  $content
     * @return string
     */
    private function sanitizeHtml($content)
    {
        // 安全なHTMLタグのみを許可
        $allowedTags = '<p><br><strong><b><em><i><u><a><img><ul><ol><li><h1><h2><h3><h4><h5><h6><span><div><table><tr><td><th><thead><tbody>';
        
        // 危険なタグや属性を除去
        $content = strip_tags($content, $allowedTags);
        
        // JavaScriptの除去
        $content = preg_replace('/on\w+="[^"]*"/i', '', $content);
        $content = preg_replace('/on\w+=\'[^\']*\'/i', '', $content);
        $content = preg_replace('/javascript:/i', '', $content);
        
        return $content;
    }
}
