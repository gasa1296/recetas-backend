import type { AxiosResponse } from 'axios'
import api from '../services/axios'
import type { ApiResponse, Paginated, QueryParams, Room, RoomPayload } from '../types'

export function listRooms(
  params?: QueryParams,
): Promise<AxiosResponse<ApiResponse<Room[]>>> {
  return api.get('/rooms', { params })
}

export function getRoom(
  id: number | string,
): Promise<AxiosResponse<ApiResponse<Room>>> {
  return api.get(`/rooms/${id}`)
}

export function createRoom(
  payload: RoomPayload,
): Promise<AxiosResponse<ApiResponse<Room>>> {
  return api.post('/rooms', payload)
}

export function updateRoom(
  id: number | string,
  payload: RoomPayload,
): Promise<AxiosResponse<ApiResponse<Room>>> {
  return api.put(`/rooms/${id}`, payload)
}

export function deleteRoom(
  id: number | string,
): Promise<AxiosResponse<ApiResponse<unknown>>> {
  return api.delete(`/rooms/${id}`)
}
