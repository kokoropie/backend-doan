<?php

namespace Database\Seeders;

use App\Enums\FeedbackStatus;
use App\Models\Feedback;
use App\Models\Score;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $messages = [
            'Tôi không hiểu vì sao con tôi lại bị điểm thấp như vậy.',
            'Tôi muốn xem lại bài kiểm tra để hiểu rõ vì sao con bị trừ điểm.',
            'Con tôi nói đã làm đúng nhưng lại bị chấm sai.',
            'Điểm số không phản ánh đúng năng lực thực tế của cháu.',
            'Tôi nghi ngờ có sự nhầm lẫn trong quá trình chấm điểm.',
            'Con tôi bị điểm kém dù đã học bài rất kỹ.',
            'Tôi đề nghị được đối chiếu lại bài làm của cháu với đáp án.',
            'Giáo viên có thể giải thích rõ hơn lý do con bị điểm thấp không?',
            'Tôi thấy con làm bài tốt nhưng không hiểu sao điểm lại thấp.',
            'Có thể nào xảy ra lỗi khi nhập điểm vào hệ thống không?',
            'Con tôi bảo nộp bài đầy đủ nhưng vẫn bị điểm kém.',
            'Tôi mong được trao đổi trực tiếp với giáo viên về điểm số của cháu.',
            'Tại sao các bạn khác cùng làm đề nhưng điểm lại cao hơn nhiều?',
            'Tôi thấy điểm số của con dao động thất thường không rõ lý do.',
            'Con tôi bị đánh giá thấp hơn khả năng thật sự của bé.',
        ];
        shuffle($messages);
        $scoreId = Feedback::whereNotNull('score_id')->pluck('score_id')->toArray();
        $scores = Score::whereNotIn('id', $scoreId)->with(['teacher'])->inRandomOrder()->take(count($messages))->get();
        $parents = User::hasParent()->pluck('id');
        $status = collect(FeedbackStatus::cases());
        foreach ($scores as $score) {
            Feedback::create([
                'student_id' => $score->student_id,
                'parent_id' => $parents->random(),
                'score_id' => $score->id,
                'teacher_id' => $score->teacher->id,
                'message' => array_pop($messages),
                'status' => $status->random()
            ]);
        }
    }
}
