<?php

namespace App\Controllers;

use App\Models\NotificationModel;

class Notifications extends BaseController
{
    protected $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    public function get()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Not logged in'
            ]);
        }

        $userId = session()->get('user_id');
        $unreadCount = $this->notificationModel->getUnreadCount($userId);
        $notifications = $this->notificationModel->getNotificationsForUser($userId);

        return $this->response->setJSON([
            'success' => true,
            'unread_count' => $unreadCount,
            'notifications' => $notifications
        ]);
    }

    public function mark_as_read($id)
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Not logged in'
            ]);
        }

        $userId = session()->get('user_id');
        $notification = $this->notificationModel->find($id);

        if (!$notification || $notification['user_id'] != $userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Notification not found'
            ]);
        }

        if ($this->notificationModel->markAsRead($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to mark notification as read'
            ]);
        }
    }

    public function delete($id)
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Not logged in'
            ]);
        }

        $userId = session()->get('user_id');
        $notification = $this->notificationModel->find($id);

        if (!$notification || $notification['user_id'] != $userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Notification not found'
            ])->setStatusCode(404);
        }

        if ($this->notificationModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Notification deleted'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to delete notification'
        ])->setStatusCode(500);
    }
}

