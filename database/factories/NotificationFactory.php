<?php

namespace Database\Factories;

use App\Enums\ReceiverType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $notifications = [
            [
                'title' => 'Thông báo nghỉ học do thời tiết xấu',
                'content' => 'Do ảnh hưởng của bão, học sinh toàn trường sẽ nghỉ học vào ngày mai (15/6). Mong phụ huynh theo dõi thêm thông tin từ nhà trường.'
            ],
            [
                'title' => 'Lịch thi học kỳ II',
                'content' => 'Kỳ thi học kỳ II sẽ diễn ra từ ngày 20/6 đến 25/6. Đề nghị học sinh ôn tập nghiêm túc và đến trường đúng giờ.'
            ],
            [
                'title' => 'Thông báo họp phụ huynh cuối kỳ',
                'content' => 'Nhà trường kính mời quý phụ huynh đến dự buổi họp tổng kết học kỳ vào lúc 7h30 sáng Chủ Nhật ngày 30/6 tại lớp học của con.'
            ],
            [
                'title' => 'Cập nhật điểm thi giữa kỳ',
                'content' => 'Điểm thi giữa kỳ đã được cập nhật trên hệ thống. Phụ huynh và học sinh vui lòng đăng nhập để theo dõi.'
            ],
            [
                'title' => 'Tổ chức ngoại khóa cuối năm',
                'content' => 'Trường sẽ tổ chức buổi dã ngoại cho học sinh khối 6-9 vào ngày 28/6. Học sinh vui lòng đăng ký với GVCN trước ngày 20/6.'
            ],
            [
                'title' => 'Nhắc nhở nộp học phí tháng 6',
                'content' => 'Phụ huynh vui lòng hoàn tất học phí tháng 6 trước ngày 10/6 để đảm bảo quyền lợi học tập cho các em học sinh.'
            ],
            [
                'title' => 'Thông báo kiểm tra sức khỏe định kỳ',
                'content' => 'Học sinh toàn trường sẽ được kiểm tra sức khỏe định kỳ vào ngày 18/6. Phụ huynh vui lòng chuẩn bị giấy khám sức khỏe theo mẫu.'
            ],
            [
                'title' => 'Khen thưởng học sinh tiêu biểu',
                'content' => 'Danh sách học sinh đạt thành tích xuất sắc trong học kỳ đã được công bố. Nhà trường sẽ tổ chức lễ khen thưởng vào sáng thứ Hai tuần sau.'
            ],
            [
                'title' => 'Cảnh báo an toàn giao thông',
                'content' => 'Nhà trường đề nghị phụ huynh nhắc nhở học sinh tuân thủ luật giao thông khi đến trường, đặc biệt tại các điểm giao cắt gần cổng trường.'
            ],
            [
                'title' => 'Thông báo đổi thời khóa biểu',
                'content' => 'Thời khóa biểu các lớp sẽ có thay đổi từ ngày 17/6. Học sinh theo dõi bảng tin hoặc website trường để cập nhật chi tiết.'
            ],
        ];
        $notification = collect($notifications)->random();

        return [
            'title' => $notification['title'],
            'message' => $notification['content'],
            'receiver_type' => fake()->randomElement(collect(ReceiverType::cases())->pluck('value')->toArray())
        ];
    }
}
