import type { AxiosResponse } from 'axios'
import api from '../services/axios'
import type { ApiResponse, Medicament, Paginated, QueryParams } from '../types'

export function listMedicaments(
  params?: QueryParams,
): Promise<AxiosResponse<ApiResponse<Paginated<Medicament>>>> {
  return api.get('/medicaments', { params })
}

export function getMedicament(
  id: number | string,
): Promise<AxiosResponse<ApiResponse<Medicament>>> {
  return api.get(`/medicaments/${id}`)
}
