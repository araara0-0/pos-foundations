<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\Files\UploadedFile;

class Users extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();
        $users = $userModel->findAll();
        return view('users/index', ['users' => $users]);
    }

    public function new()
    {
        return view('users/form', [
            'user' => ['username' => '', 'full_name' => '', 'role' => 'Staff', 'avatar' => null],
            'isEdit' => false,
        ]);
    }

    public function create()
    {
        $model = new UserModel();
        $data = $this->userData();
        $password = (string) $this->request->getPost('password');
        if (! $this->validateUser($data) || ! $this->validatePassword($password, true)) {
            return view('users/form', ['user' => $data, 'isEdit' => false]);
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        $model->insert($data);
        return redirect()->to('/users')->with('success', 'User account created.');
    }

    public function edit(int $id)
    {
        $user = (new UserModel())->find($id);
        if ($user === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('users/form', ['user' => $user, 'isEdit' => true]);
    }

    public function update(int $id)
    {
        $model = new UserModel();
        $user = $model->find($id);
        if ($user === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = $this->userData();
        $password = (string) $this->request->getPost('password');
        $userValid = $this->validateUser($data, $id);
        if (! $this->validatePassword($password, false) || ! $userValid) {
            $data['id'] = $id;
            $data['avatar'] = $user['avatar'] ?? null;
            return view('users/form', ['user' => $data, 'isEdit' => true]);
        }

        $avatar = $this->prepareAvatar($user['avatar'] ?? null);
        if ($avatar === false) {
            $data['id'] = $id;
            $data['avatar'] = $user['avatar'] ?? null;
            return view('users/form', ['user' => $data, 'isEdit' => true]);
        }
        if ($avatar !== null) {
            $data['avatar'] = $avatar;
        }

        if ($password !== '') {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $model->update($id, $data);
        return redirect()->to('/users')->with('success', 'User account updated.');
    }

    private function userData(): array
    {
        return [
            'username' => trim((string) $this->request->getPost('username')),
            'full_name' => trim((string) $this->request->getPost('full_name')),
            'role' => trim((string) $this->request->getPost('role')) ?: 'Staff',
        ];
    }

    private function validateUser(array $data, ?int $id = null): bool
    {
        $rules = [
            'username' => 'required|max_length[50]',
            'full_name' => 'required|max_length[100]',
            'role' => 'permit_empty|max_length[50]',
        ];
        if (! $this->validateData($data, $rules)) {
            return false;
        }

        $model = new UserModel();
        $existing = $model->where('username', $data['username'])->first();
        if ($existing !== null && (int) $existing['id'] !== $id) {
            $this->validator->setError('username', 'That username is already in use.');
            return false;
        }

        return true;
    }

    private function validatePassword(string $password, bool $required): bool
    {
        if ($password === '' && ! $required) {
            return true;
        }

        if (strlen($password) < 8 || strlen($password) > 72) {
            service('validation')->setError('password', 'Password must be between 8 and 72 characters.');
            return false;
        }

        return true;
    }

    /** @return string|null|false The stored filename, null when no upload was supplied, or false on validation failure. */
    private function prepareAvatar(?string $oldAvatar): string|null|false
    {
        /** @var UploadedFile|null $upload */
        $upload = $this->request->getFile('avatar');
        if ($upload === null || $upload->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if (! $upload->isValid() || $upload->getSize() > 2 * 1024 * 1024) {
            $this->validator->setError('avatar', 'The profile picture must be a JPG or PNG no larger than 2MB.');
            return false;
        }

        $imageInfo = @getimagesize($upload->getTempName());
        $mime = $imageInfo['mime'] ?? '';
        if (! in_array($mime, ['image/jpeg', 'image/png'], true)) {
            $this->validator->setError('avatar', 'The profile picture must be a JPG or PNG no larger than 2MB.');
            return false;
        }

        $directory = FCPATH . 'uploads';
        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            $this->validator->setError('avatar', 'The profile picture could not be saved.');
            return false;
        }

        $filename = bin2hex(random_bytes(16)) . ($mime === 'image/png' ? '.png' : '.jpg');
        $destination = $directory . DIRECTORY_SEPARATOR . $filename;
        try {
            service('image')->withFile($upload->getTempName())->fit(256, 256, 'center')->save($destination, 85);
        } catch (\Throwable) {
            $this->validator->setError('avatar', 'The profile picture could not be prepared.');
            return false;
        }

        if ($oldAvatar && is_file($directory . DIRECTORY_SEPARATOR . basename($oldAvatar))) {
            @unlink($directory . DIRECTORY_SEPARATOR . basename($oldAvatar));
        }
        return $filename;
    }
}
