<?php

declare(strict_types=1);
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CompletedTask as CompletedTaskModel;

class CompletedTaskController extends Controller
{
    /**
     * タスク一覧ページ を表示する
     *
     * @return \Illuminate\View\View
     */
     public function list()
     {
         // 1Page辺りの表示アイテム数を設定
        $per_page = 5;

        //  一覧の取得
        $list = $this->getListBuilder()
                     ->paginate($per_page);
                        // ->get();
        /*
        $sql = $this->getListBuilder()
                    ->tosql();
        // echo "<pre>\n"; var_dump($sql, $list); exit;
        var_dump($sql);
        */

        return view('task.completed_list', ['list' => $list]);
     }
     
    /**
     * 「単一のタスク」Modelの取得
     */
    protected function getTaskModel($task_id)
    {
         // task_idのレコードを取得する
        $task = CompletedTaskModel::find($task_id);
        if ($task === null) {
            return null;
        }
        // 本人以外のタスクならNGとする
        if ($task->user_id !== Auth::id()) {
            return null;
        }

        return $task;
    }

    /**
     * 「単一のタスク」の表示
     */
    protected function singleTaskRender($task_id, $template_name)
    {
        // task_idのレコードを取得する
        $task = $this->getTaskModel($task_id);
        if ($task === null) {
            return redirect('/task/list');
        }

        // テンプレートに「取得したレコード」の情報を渡す
        return view($template_name, ['task' => $task]);
    }
    
    /**
     * 一覧用の Illuminate\Database\Eloquent\Builder インスタンスの取得
     */
    protected function getListBuilder()
    {
        return CompletedTaskModel::where('user_id', Auth::id())
                        ->orderBy('priority', 'DESC')
                        ->orderBy('period')
                        ->orderBy('created_at');
    }
}
